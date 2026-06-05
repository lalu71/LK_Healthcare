@extends('layouts.app')
@section('title', $patient->user->name)
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center gap-5">
        <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-extrabold text-2xl">
            {{ strtoupper(substr($patient->user->name,0,1)) }}
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-extrabold text-slate-900">{{ $patient->user->name }}</h1>
            <div class="text-sm text-slate-500">{{ $patient->user->email }} · {{ $patient->patient_id }}</div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold mb-3">{{ __('Medical Info') }}</h3>
            <div class="space-y-1.5 text-sm">
                <div><span class="text-slate-500">{{ __('Blood group:') }}</span> <span class="font-bold text-rose-600">{{ $patient->blood_group ?? '—' }}</span></div>
                <div><span class="text-slate-500">{{ __('DOB:') }}</span> {{ optional($patient->dob)->format('d M Y') ?? '—' }}</div>
                <div><span class="text-slate-500">{{ __('Gender:') }}</span> {{ ucfirst($patient->gender ?? '—') }}</div>
                <div><span class="text-slate-500">{{ __('Emergency:') }}</span> {{ $patient->emergency_contact ?? '—' }}</div>
                <div><span class="text-slate-500">{{ __('Allergies:') }}</span> {{ $patient->allergies ?? '—' }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold mb-3">{{ __('Totals') }}</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-teal-50"><div class="text-xs text-slate-500">{{ __('Appointments') }}</div><div class="text-2xl font-extrabold text-teal-700">{{ $patient->appointments->count() }}</div></div>
                <div class="p-3 rounded-xl bg-violet-50"><div class="text-xs text-slate-500">{{ __('Prescriptions') }}</div><div class="text-2xl font-extrabold text-violet-700">{{ $patient->prescriptions->count() }}</div></div>
                <div class="p-3 rounded-xl bg-amber-50"><div class="text-xs text-slate-500">{{ __('Records') }}</div><div class="text-2xl font-extrabold text-amber-700">{{ $patient->medicalRecords->count() }}</div></div>
                <div class="p-3 rounded-xl bg-emerald-50"><div class="text-xs text-slate-500">{{ __('Lab tests') }}</div><div class="text-2xl font-extrabold text-emerald-700">{{ $patient->labBookings->count() }}</div></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold mb-3">{{ __('Appointments') }}</h3>
        @forelse($patient->appointments as $apt)
            <div class="py-3 border-b border-slate-50 last:border-0 flex justify-between items-center text-sm">
                <div><span class="font-semibold">Dr. {{ $apt->doctor->user->name }}</span> · <span class="text-slate-500">{{ $apt->appointment_date->format('d M Y, h:i A') }}</span></div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">{{ __(ucfirst($apt->status)) }}</span>
            </div>
        @empty
            <div class="text-sm text-slate-400">{{ __('No appointments.') }}</div>
        @endforelse
    </div>
</div>
@endsection
