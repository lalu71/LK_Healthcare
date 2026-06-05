@extends('layouts.app')
@section('title', __('My Appointments'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="calendar" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('My Appointments') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('All your appointments, past and upcoming.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <x-list-filter :action="route('patient.appointments.index')" :q="$q" :placeholder="__('Search doctor')" :hasFilters="!empty($q) || !empty($status)">
                <select name="status" class="h-11 rounded-lg border-slate-200 text-sm font-medium text-slate-700 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">{{ __('All status') }}</option>
                    @foreach(['pending','confirmed','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(($status ?? '')===$s)>{{ __(ucfirst($s)) }}</option>
                    @endforeach
                </select>
            </x-list-filter>
            <a href="{{ route('patient.book') }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700"><x-icon name="plus" class="h-4 w-4"/> {{ __('New') }}</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        @if($appointments->isEmpty())
            <div class="p-12 text-center">
                <x-icon name="calendar" class="h-14 w-14 mx-auto text-slate-300"/>
                <p class="mt-3 text-slate-500">{{ __('No appointments yet.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Doctor') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Date & Time') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Payment') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($appointments as $apt)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">Dr. {{ $apt->doctor->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ __($apt->doctor->specialization->name ?? '-') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-slate-800">{{ $apt->appointment_date->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-slate-500">{{ $apt->appointment_date->translatedFormat('h:i A') }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                                        {{ $apt->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                        {{ $apt->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $apt->status === 'completed' ? 'bg-teal-100 text-teal-700' : '' }}
                                        {{ $apt->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}">
                                        {{ __($apt->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($apt->payment_status === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase bg-emerald-100 text-emerald-700">Paid</span>
                                    @else
                                        <a href="{{ route('payment.show', ['type'=>'appointment','id'=>$apt->id]) }}" class="text-xs font-semibold text-rose-600 hover:underline">{{ __('Pay Now') }}</a>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right space-x-3">
                                    @php
                                        $canCancel = in_array($apt->status, ['pending','confirmed']) && !$apt->appointment_date->isPast();
                                        $hasRx = (bool) $apt->prescription;
                                        $needsPay = $apt->payment_status !== 'paid' && $apt->status !== 'cancelled';
                                    @endphp

                                    @if($hasRx)
                                        <a href="{{ route('patient.prescriptions.show', $apt->prescription->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-600 hover:underline"><x-icon name="pill" class="h-4 w-4"/> {{ __('Rx') }}</a>
                                    @endif

                                    @if($needsPay)
                                        <a href="{{ route('payment.show', ['type'=>'appointment','id'=>$apt->id]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:underline"><x-icon name="credit-card" class="h-4 w-4"/> {{ __('Pay') }}</a>
                                    @endif

                                    @if($canCancel)
                                        <form method="POST" action="{{ route('patient.appointments.destroy',$apt->id) }}" class="inline" onsubmit="return confirm('{{ __('Cancel this appointment?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:underline"><x-icon name="x" class="h-4 w-4"/> {{ __('Cancel') }}</button>
                                        </form>
                                    @endif

                                    @if(!$hasRx && !$needsPay && !$canCancel)
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-50">{{ $appointments->links() }}</div>
        @endif
    </div>
</div>
@endsection
