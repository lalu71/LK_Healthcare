@extends('layouts.app')
@section('title', __('Payment'))
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="credit-card" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Complete Payment') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Secure checkout') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-4">{{ __('Order summary') }}</h3>

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-5 space-y-2 text-sm">
            @if($type === 'appointment')
                <div class="flex justify-between"><span>{{ __('Doctor') }}</span><span class="font-semibold">Dr. {{ $record->doctor->user->name }}</span></div>
                <div class="flex justify-between"><span>{{ __('When') }}</span><span>{{ $record->appointment_date->format('d M Y, h:i A') }}</span></div>
                <div class="flex justify-between"><span>{{ __('Consultation fee') }}</span><span class="font-extrabold">₹{{ number_format($amount,2) }}</span></div>
            @elseif($type === 'lab')
                <div class="flex justify-between"><span>{{ __('Test') }}</span><span class="font-semibold">{{ $record->labTest->name }}</span></div>
                <div class="flex justify-between"><span>{{ __('Booking date') }}</span><span>{{ $record->booking_date->format('d M Y, h:i A') }}</span></div>
                <div class="flex justify-between"><span>{{ __('Amount') }}</span><span class="font-extrabold">₹{{ number_format($amount,2) }}</span></div>
            @else
                <div class="flex justify-between"><span>{{ __('Order') }}</span><span class="font-mono">{{ $record->order_code }}</span></div>
                <div class="flex justify-between"><span>{{ __('Items') }}</span><span>{{ $record->items->count() }}</span></div>
                <div class="flex justify-between"><span>{{ __('Total') }}</span><span class="font-extrabold">₹{{ number_format($amount,2) }}</span></div>
            @endif
        </div>

        <form id="razorpay-form" method="POST" action="{{ route('payment.pay', ['type'=>$type, 'id'=>$record->id]) }}" class="mt-6">
            @csrf
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
            <button type="button" id="pay-btn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-emerald-600 text-white font-extrabold text-lg hover:bg-emerald-700 shadow-md shadow-emerald-100">
                <x-icon name="credit-card" class="h-6 w-6"/> {{ __('Pay with Razorpay') }} · ₹{{ number_format($amount,2) }}
            </button>
        </form>

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            var options = {
                "key": "{{ config('services.razorpay.key') }}",
                "amount": "{{ $razorpayOrder['amount'] }}",
                "currency": "INR",
                "name": "{{ config('app.name') }}",
                "description": "Payment for {{ ucfirst($type) }}",
                "order_id": "{{ $razorpayOrder['id'] }}",
                "handler": function (response){
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('razorpay-form').submit();
                },
                "prefill": {
                    "name": "{{ auth()->user()->name }}",
                    "email": "{{ auth()->user()->email }}"
                },
                "theme": {
                    "color": "#059669" // emerald-600
                }
            };
            var rzp = new Razorpay(options);
            document.getElementById('pay-btn').onclick = function(e){
                rzp.open();
                e.preventDefault();
            }
        </script>
    </div>
</div>
@endsection
