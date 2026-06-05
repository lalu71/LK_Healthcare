<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\LabBooking;
use App\Models\Payment;
use App\Models\PharmacyOrder;
use App\Services\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    private function resolve(string $type, int $id)
    {
        return match ($type) {
            'appointment' => ['model' => Appointment::class, 'record' => Appointment::with('doctor.user','patient.user')->findOrFail($id)],
            'lab' => ['model' => LabBooking::class, 'record' => LabBooking::with('labTest','patient.user')->findOrFail($id)],
            'pharmacy' => ['model' => PharmacyOrder::class, 'record' => PharmacyOrder::with('items.medicine','patient.user')->findOrFail($id)],
            default => abort(404),
        };
    }

    public function show(Request $request, string $type, int $id)
    {
        $r = $this->resolve($type, $id);
        $record = $r['record'];
        $this->authorizeRecord($request, $type, $record);

        $amount = $this->amountFor($type, $record);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $razorpayOrder = $api->order->create([
            'receipt'         => 'rcptid_' . $record->id . '_' . time(),
            'amount'          => (int) ($amount * 100), // amount in paise
            'currency'        => 'INR',
            'payment_capture' => 1 // auto capture
        ]);

        return view('payments.show', [
            'type' => $type,
            'record' => $record,
            'amount' => $amount,
            'razorpayOrder' => $razorpayOrder,
        ]);
    }

    public function pay(Request $request, string $type, int $id)
    {
        $r = $this->resolve($type, $id);
        $record = $r['record'];
        $this->authorizeRecord($request, $type, $record);
        $amount = $this->amountFor($type, $record);

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment verification failed: ' . $e->getMessage());
        }

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'payable_type' => $r['model'],
            'payable_id' => $record->id,
            'receipt_no' => 'RCP-'.strtoupper(Str::random(8)),
            'amount' => $amount,
            'method' => 'razorpay',
            'status' => 'success',
            'transaction_ref' => $request->razorpay_payment_id,
        ]);

        // Mark source paid
        if ($type === 'appointment') {
            $record->update(['payment_status' => 'paid', 'status' => 'confirmed']);
        } elseif ($type === 'lab') {
            $record->update(['payment_status' => 'paid']);
        } elseif ($type === 'pharmacy') {
            $record->update(['payment_status' => 'paid', 'status' => 'packed']);
        }

        Notify::send($request->user()->id, 'Payment successful', 'Receipt '.$payment->receipt_no.' · ₹'.number_format($amount,0), route('payment.receipt', $payment->id), 'credit-card');

        return redirect()->route('payment.receipt', $payment->id)->with('success', 'Payment successful!');
    }

    public function receipt(Payment $payment, Request $request)
    {
        abort_unless($payment->user_id === $request->user()->id || $request->user()->hasRole('admin'), 403);
        return view('payments.receipt', compact('payment'));
    }

    private function amountFor(string $type, $record): float
    {
        return match ($type) {
            'appointment' => (float) ($record->doctor->consultation_fee ?? 0),
            'lab' => (float) $record->amount,
            'pharmacy' => (float) $record->total,
        };
    }

    private function authorizeRecord(Request $request, string $type, $record): void
    {
        $user = $request->user();
        $ownerId = $record->patient?->user_id;
        abort_unless($ownerId === $user->id || $user->hasRole('admin'), 403);
    }
}
