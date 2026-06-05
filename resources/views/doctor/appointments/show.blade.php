@extends('layouts.app')
@section('title', __('Appointment'))
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="calendar" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Appointment Details') }}</h1>
            <p class="text-slate-500 text-sm">{{ $appointment->appointment_date->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-900 mb-3">{{ __('Patient Information') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">{{ __('Name') }}</span><span class="font-semibold">{{ $appointment->patient->user->name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">{{ __('Patient ID') }}</span><span class="font-mono">{{ $appointment->patient->patient_id ?? '-' }}</span></div>
                @if($appointment->patient->dob)
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Age') }}</span><span>{{ $appointment->patient->dob->age }}</span></div>
                @endif
                @if($appointment->patient->blood_group)
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Blood group') }}</span><span class="font-bold text-rose-600">{{ $appointment->patient->blood_group }}</span></div>
                @endif
                @if($appointment->patient->allergies)
                    <div class="pt-2 border-t border-slate-200"><span class="text-slate-500">{{ __('Allergies') }}:</span> <span class="text-amber-700">{{ $appointment->patient->allergies }}</span></div>
                @endif
                @if($appointment->reason)
                    <div class="pt-2 border-t border-slate-200"><span class="text-slate-500">{{ __('Reason') }}:</span> {{ $appointment->reason }}</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="font-bold text-slate-900 mb-3">{{ __('Update Status') }}</h3>
            <form method="POST" action="{{ route('doctor.appointments.update-status',$appointment->id) }}" class="space-y-4">
                @csrf @method('PATCH')
                <select name="status" class="w-full rounded-lg border-slate-300">
                    <option value="confirmed" @selected($appointment->status==='confirmed')>Confirm</option>
                    <option value="completed" @selected($appointment->status==='completed')>Mark as Completed</option>
                    <option value="cancelled" @selected($appointment->status==='cancelled')>Cancel</option>
                </select>
                <textarea name="doctor_notes" rows="3" placeholder="{{ __('Doctor notes (optional)') }}" class="w-full rounded-lg border-slate-300">{{ $appointment->doctor_notes }}</textarea>
                <button class="w-full px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">{{ __('Update') }}</button>
            </form>

            @if(!$appointment->prescription)
                <a href="{{ route('doctor.prescriptions.create', ['appointment_id'=>$appointment->id]) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">
                    <x-icon name="pill" class="h-5 w-5"/> {{ __('Write Prescription') }}
                </a>
            @else
                <a href="{{ route('doctor.prescriptions.show',$appointment->prescription->id) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                    <x-icon name="eye" class="h-5 w-5"/> {{ __('View Prescription') }}
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
