<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\PharmacyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PharmacyApiController extends Controller
{
    public function medicines(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $list = Medicine::where('is_active', true)
            ->when($q, fn ($qr) => $qr->where('name', 'like', "%$q%"))
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name', 'category', 'manufacturer', 'price', 'stock', 'unit', 'requires_prescription']);

        return response()->json(['data' => $list]);
    }

    public function myOrders(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $orders = PharmacyOrder::with('items.medicine')
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_code' => $o->order_code,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'subtotal' => (float) $o->subtotal,
                'delivery_fee' => (float) $o->delivery_fee,
                'total' => (float) $o->total,
                'delivery_address' => $o->delivery_address,
                'delivery_phone' => $o->delivery_phone,
                'created_at' => $o->created_at->toIso8601String(),
                'items' => $o->items->map(fn ($i) => [
                    'id' => $i->id,
                    'medicine_id' => $i->medicine_id,
                    'medicine_name' => $i->medicine?->name,
                    'quantity' => (int) $i->quantity,
                    'price' => (float) $i->price,
                    'line_total' => (float) $i->line_total,
                ]),
            ]);

        return response()->json(['data' => $orders]);
    }

    public function placeOrder(Request $request)
    {
        $patient = $request->user()->patient;
        abort_unless($patient, 404, 'Patient profile not found');

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
            'delivery_address' => 'required|string|max:500',
            'delivery_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $subtotal = 0;
        $lineRows = [];
        foreach ($data['items'] as $row) {
            $med = Medicine::findOrFail($row['medicine_id']);
            $line = (float) $med->price * (int) $row['quantity'];
            $subtotal += $line;
            $lineRows[] = [
                'medicine_id' => $med->id,
                'quantity' => (int) $row['quantity'],
                'price' => (float) $med->price,
                'line_total' => $line,
            ];
        }
        $deliveryFee = $subtotal >= 500 ? 0 : 40;
        $total = $subtotal + $deliveryFee;

        $order = PharmacyOrder::create([
            'patient_id' => $patient->id,
            'order_code' => 'PHM-' . strtoupper(Str::random(8)),
            'status' => 'placed',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_address' => $data['delivery_address'],
            'delivery_phone' => $data['delivery_phone'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($lineRows as $row) {
            $order->items()->create($row);
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'subtotal' => (float) $order->subtotal,
                'delivery_fee' => (float) $order->delivery_fee,
                'total' => (float) $order->total,
                'status' => $order->status,
            ],
        ], 201);
    }
}
