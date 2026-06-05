@extends('layouts.app')
@section('title', __('Process Order'))
@section('content')
@php
    $estimated = $prescription->estimated_total;
    $paidPayment = $prescription->payments->firstWhere('status', 'success');
    $isPaid = $prescription->payment_status === 'paid';
    $isDispensed = $prescription->status === 'dispensed';
    $patient = $prescription->patient;
    $doctor = $prescription->doctor;
    $steps = [
        ['key' => 'requested', 'label' => __('Requested'), 'done' => true],
        ['key' => 'paid',      'label' => __('Paid'),      'done' => $isPaid],
        ['key' => 'dispensed', 'label' => __('Dispensed'), 'done' => $isDispensed],
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">{{ __('Process Order') }}</h1>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                    RX <span class="text-emerald-600">{{ $prescription->prescription_code }}</span>
                </p>
            </div>

            {{-- Stepper --}}
            <div class="flex items-center gap-2 sm:gap-3 sm:pl-6 sm:border-l sm:border-slate-100">
                @foreach($steps as $i => $step)
                    <div class="flex items-center gap-2">
                        <div class="h-7 w-7 rounded-full flex items-center justify-center text-[11px] font-black
                            {{ $step['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                            @if($step['done'])
                                <x-icon name="check" class="h-4 w-4"/>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wide {{ $step['done'] ? 'text-slate-900' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="w-6 h-px {{ $step['done'] ? 'bg-emerald-200' : 'bg-slate-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pharmacist.prescriptions.pdf', $prescription->id) }}"
               class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 transition flex items-center gap-2">
                <x-icon name="download" class="h-4 w-4"/> {{ __('Download PDF') }}
            </a>
            <button type="button"
                    onclick="lkPrintRx('{{ route('pharmacist.prescriptions.print', $prescription->id) }}')"
                    class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition flex items-center gap-2">
                <x-icon name="printer" class="h-4 w-4"/> {{ __('Print') }}
            </button>
            @if(! $isDispensed)
                <form method="POST" action="{{ route('pharmacist.prescriptions.update-status', $prescription->id) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="dispensed">
                    <button @disabled(! $isPaid)
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-emerald-600"
                        @if(! $isPaid) title="{{ __('Record payment first') }}" @endif>
                        <x-icon name="check" class="h-4 w-4"/> {{ __('Mark as Dispensed') }}
                    </button>
                </form>
            @else
                <span class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                    <x-icon name="check" class="h-4 w-4"/> {{ __('Dispensed') }}
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- LEFT: main content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Medicines --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <x-icon name="pill" class="h-5 w-5"/>
                        </div>
                        <h2 class="font-bold text-slate-900">{{ __('Medicines to Dispense') }}</h2>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-50 px-3 py-1 rounded-lg">
                        {{ trans_choice('{1} :count item|[2,*] :count items', $prescription->items->count(), ['count' => $prescription->items->count()]) }}
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($prescription->items as $item)
                        @php
                            $med = $item->medicine;
                            $stock = $med?->stock ?? 0;
                            $isLinked = (bool) $med;
                            $stockState = ! $isLinked
                                ? ['label' => __('Not in catalog'), 'class' => 'bg-slate-100 text-slate-600']
                                : ($stock <= 0
                                    ? ['label' => __('Out of stock'), 'class' => 'bg-rose-100 text-rose-700']
                                    : ($stock < 50
                                        ? ['label' => __('Low stock').' · '.$stock, 'class' => 'bg-amber-100 text-amber-700']
                                        : ['label' => __('In stock').' · '.$stock, 'class' => 'bg-emerald-100 text-emerald-700']));
                        @endphp
                        <div class="px-5 py-5 flex flex-col sm:flex-row sm:items-start gap-4">
                            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <x-icon name="pill" class="h-6 w-6"/>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    <h3 class="text-base font-extrabold text-slate-900">{{ $item->medicine_name }}</h3>
                                    @if($med?->unit)
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 py-0.5 bg-slate-100 rounded">{{ $med->unit }}</span>
                                    @endif
                                    <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded {{ $stockState['class'] }}">
                                        {{ $stockState['label'] }}
                                    </span>
                                    @if($med?->price)
                                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest px-2 py-0.5 bg-emerald-50 rounded ml-auto">
                                            ₹{{ number_format($med->price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <div class="bg-slate-50 rounded-lg px-3 py-2">
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ __('Dose') }}</div>
                                        <div class="font-bold text-slate-900">{{ $item->dosage }}</div>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg px-3 py-2">
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ __('Frequency') }}</div>
                                        <div class="font-bold text-slate-900">{{ $item->frequency }}</div>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg px-3 py-2">
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ __('Duration') }}</div>
                                        <div class="font-bold text-slate-900">{{ $item->duration }}</div>
                                    </div>
                                </div>

                                @if($item->instructions)
                                    <div class="mt-3 p-3 rounded-lg bg-amber-50 border border-amber-100 flex items-start gap-2">
                                        <x-icon name="chat" class="h-4 w-4 text-amber-600 mt-0.5 shrink-0"/>
                                        <p class="text-xs text-amber-800 font-semibold leading-relaxed">{{ $item->instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- Diagnosis & Advice --}}
            @if($prescription->diagnosis || $prescription->advice)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($prescription->diagnosis)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="h-8 w-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center"><x-icon name="stethoscope" class="h-4 w-4"/></div>
                                <h3 class="text-[11px] font-bold text-slate-900 uppercase tracking-widest">{{ __('Diagnosis') }}</h3>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $prescription->diagnosis }}</p>
                        </div>
                    @endif
                    @if($prescription->advice)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="h-8 w-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><x-icon name="check" class="h-4 w-4"/></div>
                                <h3 class="text-[11px] font-bold text-slate-900 uppercase tracking-widest">{{ __('Advice') }}</h3>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $prescription->advice }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Payment --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <x-icon name="credit-card" class="h-5 w-5"/>
                        </div>
                        <h2 class="font-bold text-slate-900">{{ __('Payment') }}</h2>
                    </div>
                    @if($isPaid)
                        <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest border border-emerald-100">{{ __('Paid') }}</span>
                    @else
                        <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-widest border border-amber-100">{{ __('Pending') }}</span>
                    @endif
                </div>

                <div class="p-5">
                    @if($isPaid && $paidPayment)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Receipt') }}</div>
                                <div class="text-sm font-bold text-slate-900">{{ $paidPayment->receipt_no }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Amount') }}</div>
                                <div class="text-sm font-bold text-emerald-600">₹{{ number_format($paidPayment->amount, 2) }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Method') }}</div>
                                <div class="text-sm font-bold text-slate-900 uppercase">{{ $paidPayment->method }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Reference') }}</div>
                                <div class="text-sm font-bold text-slate-900 truncate">{{ $paidPayment->transaction_ref ?: '—' }}</div>
                            </div>
                        </div>
                    @else
                        @php
                            $createLinkUrl = route('pharmacist.prescriptions.payment-link', $prescription->id);
                            $checkLinkUrl = route('pharmacist.prescriptions.payment-link.check', ['prescription' => $prescription->id, 'linkId' => '__ID__']);
                        @endphp
                        <form method="POST" action="{{ route('pharmacist.prescriptions.payment', $prescription->id) }}"
                              x-data="rzpPayment({
                                  initialMethod: '{{ old('method', 'cash') }}',
                                  initialAmount: '{{ old('amount', number_format($estimated, 2, '.', '')) }}',
                                  createLinkUrl: @js($createLinkUrl),
                                  checkLinkUrlTpl: @js($checkLinkUrl),
                                  csrf: @js(csrf_token()),
                              })"
                              x-init="init()">
                            @csrf
                            <div class="space-y-4">
                                {{-- Method --}}
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 block">{{ __('Payment Method') }}</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="method" value="cash" x-model="method" class="sr-only">
                                            <div style="position:relative;"
                                                 :class="method === 'cash' ? 'border-emerald-500 bg-emerald-100 shadow-md' : 'border-slate-200 bg-white hover:border-slate-300 opacity-70'"
                                                 class="p-3 pr-9 rounded-xl border-4 transition flex items-center gap-3">
                                                <div :class="method === 'cash' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-600'"
                                                     class="h-9 w-9 rounded-lg flex items-center justify-center transition">
                                                    <x-icon name="credit-card" class="h-5 w-5"/>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-slate-900">{{ __('Cash') }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">{{ __('Pay at counter') }}</div>
                                                </div>
                                                <span x-show="method === 'cash'" x-cloak class="text-emerald-600" style="position:absolute;top:8px;right:8px;line-height:0;z-index:10;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="method" value="online" x-model="method" class="sr-only">
                                            <div style="position:relative;"
                                                 :class="method === 'online' ? 'border-emerald-500 bg-emerald-100 shadow-md' : 'border-slate-200 bg-white hover:border-slate-300 opacity-70'"
                                                 class="p-3 pr-9 rounded-xl border-4 transition flex items-center gap-3">
                                                <div :class="method === 'online' ? 'bg-emerald-500 text-white' : 'bg-teal-50 text-teal-600'"
                                                     class="h-9 w-9 rounded-lg flex items-center justify-center transition">
                                                    <x-icon name="credit-card" class="h-5 w-5"/>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-slate-900">{{ __('Online') }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest">{{ __('UPI / Card') }}</div>
                                                </div>
                                                <span x-show="method === 'online'" x-cloak class="text-emerald-600" style="position:absolute;top:8px;right:8px;line-height:0;z-index:10;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2 block">{{ __('Amount') }} (₹)</label>
                                    <input type="number" step="0.01" min="1" name="amount" x-model="amount"
                                        class="w-full sm:w-64 px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-0 text-base font-bold text-slate-900" required>
                                    @if($estimated > 0)
                                        <div class="text-[10px] font-semibold text-slate-400 mt-1">{{ __('Estimated') }}: ₹{{ number_format($estimated, 2) }}</div>
                                    @endif
                                </div>

                                {{-- Razorpay Payment Link QR (only when Online is selected) --}}
                                <div x-show="method === 'online'" x-transition x-cloak
                                     class="p-5 rounded-xl bg-gradient-to-br from-teal-50 to-emerald-50 border border-teal-100">
                                    <div class="flex flex-col sm:flex-row items-center gap-5">
                                        {{-- QR --}}
                                        <div style="position:relative;min-width:11.75rem;min-height:11.75rem;" class="bg-white p-3 rounded-xl border border-teal-200 shadow-sm shrink-0 flex items-center justify-center">
                                            <template x-if="paymentUrl && !generating">
                                                <img :src="qrUrl" alt="Razorpay Payment QR" class="h-44 w-44">
                                            </template>
                                            {{-- Generating spinner --}}
                                            <template x-if="generating">
                                                <div class="h-44 w-44 flex flex-col items-center justify-center text-slate-500 text-xs font-semibold gap-2 text-center px-3">
                                                    <svg class="h-8 w-8 animate-spin text-teal-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                                    <span>{{ __('Generating link…') }}</span>
                                                </div>
                                            </template>
                                            {{-- Empty state: amount missing or zero --}}
                                            <template x-if="!generating && !paymentUrl && (!amount || parseFloat(amount) <= 0)">
                                                <div class="h-44 w-44 flex flex-col items-center justify-center text-slate-400 text-xs font-semibold gap-2 text-center px-3">
                                                    <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                                                        <x-icon name="alert" class="h-5 w-5"/>
                                                    </div>
                                                    <span>{{ __('Enter the amount above to generate the payment QR.') }}</span>
                                                </div>
                                            </template>
                                            {{-- Error state --}}
                                            <template x-if="!generating && !paymentUrl && amount && parseFloat(amount) > 0 && error">
                                                <div class="h-44 w-44 flex flex-col items-center justify-center text-rose-500 text-xs font-semibold gap-2 text-center px-3">
                                                    <div class="h-10 w-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center">
                                                        <x-icon name="x" class="h-5 w-5"/>
                                                    </div>
                                                    <span x-text="error"></span>
                                                    <button type="button" @click="generateLink()" class="text-[10px] font-bold text-teal-600 hover:underline uppercase tracking-widest mt-1">{{ __('Retry') }}</button>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Instructions --}}
                                        <div class="flex-1 min-w-0 text-center sm:text-left">
                                            <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                                                <x-icon name="credit-card" class="h-4 w-4 text-teal-600"/>
                                                <span class="text-[11px] font-bold text-teal-700 uppercase tracking-widest">{{ __('Scan to pay with Razorpay') }}</span>
                                            </div>
                                            <div class="text-sm font-semibold text-slate-700 mb-3">
                                                {{ __('Patient scans → opens Razorpay page → pays via UPI/Card/NetBanking') }}
                                                <span class="font-extrabold text-emerald-700">₹<span x-text="parseFloat(amount || 0).toFixed(2)"></span></span>.
                                                {{ __('Page will refresh automatically when payment is received.') }}
                                            </div>

                                            {{-- Payment URL (visible + copy) --}}
                                            <div class="bg-white rounded-lg border border-slate-200 px-3 py-2 flex items-center justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Payment Link') }}</div>
                                                    <a :href="paymentUrl" target="_blank" rel="noopener" class="text-xs font-bold text-teal-600 hover:underline truncate block" x-text="paymentUrl || '—'"></a>
                                                </div>
                                                <button type="button" @click="copyLink()" :disabled="!paymentUrl" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition shrink-0 disabled:opacity-50">
                                                    <span x-show="!copied">{{ __('Copy') }}</span>
                                                    <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                                                </button>
                                            </div>

                                            <div class="flex items-center gap-3 mt-2">
                                                <button type="button" @click="generateLink()" :disabled="generating || !amount"
                                                        class="text-[10px] font-bold text-teal-600 hover:underline uppercase tracking-widest disabled:opacity-50">
                                                    {{ __('Refresh link') }}
                                                </button>
                                                <span class="text-[10px] font-semibold text-slate-400">·</span>
                                                <span class="text-[10px] font-semibold text-slate-500">{{ __('Or record manually below') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2" x-show="method === 'cash'" x-cloak>
                                    <button class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                                        <x-icon name="check" class="h-4 w-4"/> {{ __('Record Payment') }}
                                    </button>
                                </div>
                                <div x-show="method === 'online'" x-cloak class="flex items-center justify-end gap-2 pt-2 text-[11px] font-semibold text-slate-500">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    {{ __('Waiting for patient payment… page will refresh automatically.') }}
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Order Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Order ID') }}</div>
                    <div class="text-xs font-bold text-slate-900">{{ $prescription->prescription_code }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Requested') }}</div>
                    <div class="text-xs font-bold text-slate-900">{{ $prescription->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Status') }}</div>
                    <div class="text-xs font-bold text-slate-900 capitalize">{{ $prescription->status }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Payment') }}</div>
                    <div class="text-xs font-bold {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }} capitalize">{{ $prescription->payment_status }}</div>
                </div>
            </div>
        </div>

        {{-- RIGHT: sidebar --}}
        <div class="space-y-6">

            {{-- Patient --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-[11px] font-bold text-slate-900 flex items-center gap-2 uppercase tracking-widest mb-4">
                    <x-icon name="user" class="h-4 w-4 text-emerald-600"/>
                    {{ __('Patient') }}
                </h3>

                <div class="flex items-center gap-3 mb-5">
                    <div class="h-14 w-14 rounded-2xl overflow-hidden ring-4 ring-emerald-50">
                        <img src="https://i.pravatar.cc/150?u={{ $patient->user->email }}" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 truncate">{{ $patient->user->name }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ $patient->patient_id }}</div>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    @if($patient->blood_group)
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-500"><x-icon name="droplet" class="h-3.5 w-3.5"/> {{ __('Blood Group') }}</span>
                            <span class="font-bold text-rose-600">{{ $patient->blood_group }}</span>
                        </div>
                    @endif
                    @if($patient->age || $patient->gender)
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-500"><x-icon name="calendar" class="h-3.5 w-3.5"/> {{ __('Age / Gender') }}</span>
                            <span class="font-bold text-slate-700">
                                {{ $patient->age ? $patient->age.' '.__('yrs') : '—' }}{{ $patient->gender ? ' / '.ucfirst($patient->gender) : '' }}
                            </span>
                        </div>
                    @endif
                    @if($patient->user->phone)
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-slate-500"><x-icon name="phone" class="h-3.5 w-3.5"/> {{ __('Phone') }}</span>
                            <span class="font-bold text-slate-700">{{ $patient->user->phone }}</span>
                        </div>
                    @endif
                    @if($patient->allergies)
                        <div class="flex items-start justify-between gap-3">
                            <span class="flex items-center gap-2 text-slate-500 shrink-0"><x-icon name="shield" class="h-3.5 w-3.5"/> {{ __('Allergies') }}</span>
                            <span class="font-bold text-rose-600 text-right">{{ $patient->allergies }}</span>
                        </div>
                    @endif
                </div>

                @if($patient->user->phone)
                    <a href="tel:{{ $patient->user->phone }}" class="mt-5 w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-50 text-slate-700 font-bold text-xs hover:bg-slate-100 transition border border-slate-200 uppercase tracking-widest">
                        <x-icon name="phone" class="h-3.5 w-3.5"/> {{ __('Contact Patient') }}
                    </a>
                @endif
            </div>

            {{-- Doctor --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-[11px] font-bold text-slate-900 flex items-center gap-2 uppercase tracking-widest mb-4">
                    <x-icon name="stethoscope" class="h-4 w-4 text-emerald-600"/>
                    {{ __('Issued By') }}
                </h3>

                <div class="flex items-center gap-3 mb-4">
                    <div class="h-14 w-14 rounded-2xl overflow-hidden ring-4 ring-slate-50">
                        <img src="https://i.pravatar.cc/150?u={{ $doctor->user->email }}" class="h-full w-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 truncate">Dr. {{ $doctor->user->name }}</div>
                        @if($doctor->specialization)
                            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest truncate">{{ $doctor->specialization->name }}</div>
                        @endif
                    </div>
                </div>

                @if($doctor->user->phone)
                    <a href="tel:{{ $doctor->user->phone }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-xs hover:bg-teal-700 transition shadow-lg shadow-teal-600/20 uppercase tracking-widest">
                        <x-icon name="phone" class="h-3.5 w-3.5"/> {{ __('Contact Doctor') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function lkPrintRx(url) {
        let frame = document.getElementById('lkPrintFrame');
        if (frame) frame.remove();
        frame = document.createElement('iframe');
        frame.id = 'lkPrintFrame';
        frame.style.cssText = 'position:fixed;right:-10000px;bottom:-10000px;width:0;height:0;border:0;';
        frame.src = url;
        document.body.appendChild(frame);
    }

    function rzpPayment(opts) {
        return {
            method: opts.initialMethod || 'cash',
            amount: opts.initialAmount || '',
            createLinkUrl: opts.createLinkUrl,
            checkLinkUrlTpl: opts.checkLinkUrlTpl,
            csrf: opts.csrf,
            paymentUrl: null,
            paymentLinkId: null,
            generating: false,
            polling: false,
            copied: false,
            error: null,
            _amountDebounce: null,
            _pollTimer: null,

            init() {
                if (this.method === 'online') this.generateLink();
                this.$watch('method', (v) => {
                    if (v === 'online') this.generateLink();
                    else this.stopPolling();
                });
                this.$watch('amount', () => {
                    if (this.method !== 'online') return;
                    clearTimeout(this._amountDebounce);
                    this._amountDebounce = setTimeout(() => this.generateLink(), 800);
                });
            },

            get qrUrl() {
                if (!this.paymentUrl) return '';
                return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=' + encodeURIComponent(this.paymentUrl);
            },

            async generateLink() {
                if (!this.amount || parseFloat(this.amount) <= 0) return;
                this.generating = true;
                this.error = null;
                this.stopPolling();
                try {
                    const res = await fetch(this.createLinkUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        body: JSON.stringify({ amount: this.amount }),
                    });
                    const data = await res.json();
                    if (!res.ok || !data.url) {
                        throw new Error(data.error || 'Failed to create link');
                    }
                    this.paymentUrl = data.url;
                    this.paymentLinkId = data.id;
                    this.startPolling();
                } catch (e) {
                    this.error = e.message || 'Error';
                    this.paymentUrl = null;
                } finally {
                    this.generating = false;
                }
            },

            startPolling() {
                this.stopPolling();
                if (!this.paymentLinkId) return;
                this.polling = true;
                const url = this.checkLinkUrlTpl.replace('__ID__', encodeURIComponent(this.paymentLinkId));
                this._pollTimer = setInterval(async () => {
                    try {
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        if (data.paid) {
                            this.stopPolling();
                            window.location.reload();
                        }
                    } catch (e) { /* keep polling */ }
                }, 4000);
            },

            stopPolling() {
                this.polling = false;
                if (this._pollTimer) {
                    clearInterval(this._pollTimer);
                    this._pollTimer = null;
                }
            },

            copyLink() {
                if (!this.paymentUrl) return;
                navigator.clipboard.writeText(this.paymentUrl);
                this.copied = true;
                setTimeout(() => this.copied = false, 1500);
            },
        };
    }
</script>
@endsection
