@extends('layouts.app')
@section('title', __('Receipt'))
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
        <div class="h-16 w-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <x-icon name="check" class="h-8 w-8"/>
        </div>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900">{{ __('Payment Successful') }}</h1>
        <p class="mt-1 text-slate-500 text-sm">{{ __('A copy has been saved to your notifications.') }}</p>

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-5 text-left space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Receipt No.') }}</span><span class="font-mono font-semibold">{{ $payment->receipt_no }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Amount') }}</span><span class="font-extrabold">₹{{ number_format($payment->amount,2) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Method') }}</span><span class="uppercase">{{ $payment->method }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Txn Ref') }}</span><span class="font-mono text-xs">{{ $payment->transaction_ref }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">{{ __('Date') }}</span><span>{{ $payment->created_at->format('d M Y, h:i A') }}</span></div>
        </div>

        <div class="mt-6 flex gap-3 justify-center">
            <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">{{ __('Go to Dashboard') }}</a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 font-semibold hover:bg-slate-50">
                <x-icon name="printer" class="h-4 w-4"/> {{ __('Print') }}
            </button>
        </div>
    </div>
</div>
@endsection
