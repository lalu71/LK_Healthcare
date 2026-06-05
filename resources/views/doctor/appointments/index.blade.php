@extends('layouts.app')
@section('title', __('Appointments'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="calendar" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('My Appointments') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Manage patient visits.') }}</p>
            </div>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-4 flex flex-wrap items-center gap-3">
        <select name="status" class="rounded-lg border-slate-300 text-sm">
            <option value="">{{ __('All status') }}</option>
            @foreach(['pending','confirmed','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status')==$s)>{{ __(ucfirst($s)) }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-slate-300 text-sm">
        <button class="px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">{{ __('Filter') }}</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        @if($appointments->isEmpty())
            <div class="p-12 text-center text-slate-400"><x-icon name="calendar" class="h-14 w-14 mx-auto text-slate-300"/><p class="mt-3">{{ __('No appointments match.') }}</p></div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">{{ __('Patient') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Date & Time') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Reason') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($appointments as $apt)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $apt->patient->user->name }}</div>
                                <div class="text-xs text-slate-500">ID: {{ $apt->patient->patient_id ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium">{{ $apt->appointment_date->translatedFormat('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $apt->appointment_date->translatedFormat('h:i A') }}</div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ Str::limit($apt->reason, 40) ?: '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                                    {{ $apt->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $apt->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $apt->status === 'completed' ? 'bg-teal-100 text-teal-700' : '' }}
                                    {{ $apt->status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ __($apt->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('doctor.appointments.show',$apt->id) }}" class="text-teal-600 font-semibold text-xs hover:underline">{{ __('Open') }} →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-50">{{ $appointments->links() }}</div>
        @endif
    </div>
</div>
@endsection
