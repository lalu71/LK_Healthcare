@extends('layouts.app')
@section('title', __('Appointments'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="calendar" class="h-6 w-6"/></div>
            <div><h1 class="text-2xl font-extrabold text-slate-900">{{ __('All Appointments') }}</h1></div>
        </div>
        {{-- Pill-box filter style --}}
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-2 h-11 flex items-center">
                <select name="status" class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                    <option value="">{{ __('All status') }}</option>
                    @foreach(['pending','confirmed','completed','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(request('status')==$s)>{{ __(ucfirst($s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-2 h-11 flex items-center">
                <input type="date" name="date" value="{{ request('date') }}"
                       class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-36">
            </div>
            <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Filter') }}</button>
            @if(request('status') || request('date'))
                <a href="{{ route('admin.appointments.index') }}"
                   class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                    <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                </a>
            @endif
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">{{ __('Patient') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Doctor') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Payment') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($appointments as $apt)
                    <tr>
                        <td class="px-5 py-3">{{ $apt->patient->user->name }}</td>
                        <td class="px-5 py-3">Dr. {{ $apt->doctor->user->name }} <span class="text-xs text-slate-500">({{ $apt->doctor->specialization->name ?? '-' }})</span></td>
                        <td class="px-5 py-3">{{ $apt->appointment_date->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">{{ __(ucfirst($apt->status)) }}</span></td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $apt->payment_status==='paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ __(ucfirst($apt->payment_status)) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50">{{ $appointments->links() }}</div>
    </div>
</div>
@endsection
