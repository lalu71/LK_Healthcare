<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\PharmacyOrder;
use App\Models\PharmacyOrderItem;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $medicines = Medicine::where('is_active', true)
            ->when($q, fn($qr) => $qr->where('name','like','%'.$q.'%'))
            ->orderBy('name')
            ->paginate(18)->withQueryString();

        $patient = $request->user()->patient;
        $orders = collect();
        if ($patient) {
            $orders = PharmacyOrder::with('items.medicine')->where('patient_id', $patient->id)->latest()->limit(5)->get();
        }
        return view('patient.pharmacy.index', compact('medicines','orders','q'));
    }

    public function order(Request $request)
    {
        $patient = $request->user()->patient;
        if (!$patient) return redirect()->route('patient.profile.edit')->with('error', 'Complete your profile first.');

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1|max:20',
            'delivery_address' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $subtotal = 0;
        $rows = [];
        foreach ($data['items'] as $row) {
            $med = Medicine::find($row['medicine_id']);
            if (!$med || !$med->is_active) continue;
            $qty = min($row['quantity'], max(1, $med->stock));
            $line = $med->price * $qty;
            $subtotal += $line;
            $rows[] = [$med, $qty, $line];
        }
        if (empty($rows)) return back()->with('error','No valid medicines.');

        $deliveryFee = $subtotal < 499 ? 40 : 0;
        $total = $subtotal + $deliveryFee;

        $order = PharmacyOrder::create([
            'patient_id' => $patient->id,
            'order_code' => 'ORD-'.strtoupper(Str::random(6)),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_address' => $data['delivery_address'],
            'delivery_phone' => $data['delivery_phone'],
            'notes' => $data['notes'] ?? null,
        ]);
        foreach ($rows as [$med, $qty, $line]) {
            PharmacyOrderItem::create([
                'pharmacy_order_id' => $order->id,
                'medicine_id' => $med->id,
                'quantity' => $qty,
                'price' => $med->price,
                'line_total' => $line,
            ]);
            $med->decrement('stock', $qty);
        }

        Notify::send($request->user()->id, 'Order placed', 'Order '.$order->order_code.' · Total ₹'.number_format($total,0), route('patient.pharmacy.index'), 'cart');

        return redirect()->route('payment.show', ['type' => 'pharmacy', 'id' => $order->id]);
    }
}
