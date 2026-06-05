<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Prescription;
use App\Services\Notify;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $status = $request->status;

        $prescriptions = Prescription::with('patient.user', 'doctor.user', 'items')
            ->when($q !== '', fn($query) => $query->where(fn($w) =>
                $w->where('prescription_code', 'like', "%{$q}%")
                  ->orWhereHas('patient.user', fn($u) => $u->where('name', 'like', "%{$q}%"))
                  ->orWhereHas('doctor.user', fn($u) => $u->where('name', 'like', "%{$q}%"))
            ))
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pharmacist.prescriptions.index', compact('prescriptions', 'q', 'status'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('patient.user', 'doctor.user', 'items.medicine', 'payments');
        return view('pharmacist.prescriptions.show', compact('prescription'));
    }

    public function pdf(Prescription $prescription)
    {
        $prescription->load('doctor.user', 'doctor.specialization', 'patient.user', 'items');
        $pdf = Pdf::loadView('pdf.prescription', compact('prescription'));
        return $pdf->download('prescription-' . $prescription->prescription_code . '.pdf');
    }

    public function print(Prescription $prescription)
    {
        $prescription->load('doctor.user', 'doctor.specialization', 'patient.user', 'items');
        $autoPrint = true;
        return view('pdf.prescription', compact('prescription', 'autoPrint'));
    }

    public function updateStatus(Request $request, Prescription $prescription)
    {
        $request->validate(['status' => 'required|in:pending,dispensed']);

        if ($request->status === 'dispensed' && $prescription->payment_status !== 'paid') {
            return back()->with('error', __('Please record the payment before dispensing.'));
        }

        $prescription->update(['status' => $request->status]);

        if ($request->status === 'dispensed') {
            // Deduct stock for items linked to inventory
            foreach($prescription->items as $item) {
                if($item->medicine_id) {
                    $item->medicine->decrement('stock', 1);
                }
            }
        }

        return back()->with('success', 'Prescription marked as ' . $request->status);
    }

    public function recordPayment(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,online',
            'transaction_ref' => 'nullable|string|max:100',
        ]);

        if ($prescription->payment_status === 'paid') {
            return back()->with('error', __('Payment already recorded for this prescription.'));
        }

        Payment::create([
            'user_id' => $prescription->patient->user_id,
            'payable_type' => Prescription::class,
            'payable_id' => $prescription->id,
            'receipt_no' => 'RCP-RX-' . strtoupper(Str::random(8)),
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'status' => 'success',
            'transaction_ref' => $validated['transaction_ref'] ?? null,
        ]);

        $prescription->update(['payment_status' => 'paid']);

        Notify::send(
            $prescription->patient->user_id,
            __('Pharmacy payment recorded'),
            '₹' . number_format($validated['amount'], 0) . ' · ' . strtoupper($validated['method']) . ' · RX ' . $prescription->prescription_code,
            null,
            'credit-card'
        );

        return back()->with('success', __('Payment recorded successfully.'));
    }

    /**
     * Create a Razorpay Payment Link for the given prescription + amount.
     * Returns JSON with the short URL the QR will encode and the link id used for polling.
     */
    public function createPaymentLink(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($prescription->payment_status === 'paid') {
            return response()->json(['error' => __('Already paid')], 400);
        }

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $link = $api->paymentLink->create([
                'amount' => (int) round($validated['amount'] * 100),
                'currency' => 'INR',
                'accept_partial' => false,
                'description' => 'LK Healthcare · RX ' . $prescription->prescription_code,
                'customer' => [
                    'name' => $prescription->patient->user->name,
                    'email' => $prescription->patient->user->email,
                    'contact' => $prescription->patient->user->phone ?? '',
                ],
                'notify' => ['sms' => false, 'email' => false],
                'reminder_enable' => false,
                'reference_id' => 'rx-' . $prescription->id . '-' . now()->timestamp,
            ]);

            return response()->json([
                'url' => $link['short_url'],
                'id' => $link['id'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Polled by the page while waiting for payment. If the Razorpay payment link
     * is paid, record a Payment row + mark the prescription as paid, then tell
     * the client to reload.
     */
    public function checkPaymentLink(Request $request, Prescription $prescription, string $linkId)
    {
        if ($prescription->payment_status === 'paid') {
            return response()->json(['paid' => true]);
        }

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $link = $api->paymentLink->fetch($linkId);

            // Razorpay statuses: created, partially_paid, expired, cancelled, paid
            if (($link['status'] ?? null) !== 'paid') {
                return response()->json(['paid' => false, 'status' => $link['status'] ?? 'unknown']);
            }

            // Mark paid + persist payment row.
            $amount = ((int) ($link['amount_paid'] ?? $link['amount'])) / 100;
            $paymentRef = collect($link['payments'] ?? [])->first()['payment_id'] ?? $linkId;

            Payment::create([
                'user_id' => $prescription->patient->user_id,
                'payable_type' => Prescription::class,
                'payable_id' => $prescription->id,
                'receipt_no' => 'RCP-RX-' . strtoupper(Str::random(8)),
                'amount' => $amount,
                'method' => 'online',
                'status' => 'success',
                'transaction_ref' => $paymentRef,
            ]);

            $prescription->update(['payment_status' => 'paid']);

            Notify::send(
                $prescription->patient->user_id,
                __('Pharmacy payment received'),
                '₹' . number_format($amount, 0) . ' · UPI / Razorpay · RX ' . $prescription->prescription_code,
                null,
                'credit-card'
            );

            return response()->json(['paid' => true]);
        } catch (\Throwable $e) {
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
