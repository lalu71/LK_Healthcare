@extends('layouts.app')
@section('title', __('Medical Profile'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="user" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Medical Profile') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Help doctors care for you better.') }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium flex items-center gap-2">
            <x-icon name="alert" class="h-4 w-4"/> {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('patient.profile.update') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Date of birth') }}</label>
                <input type="date" name="dob" value="{{ old('dob', optional($patient->dob)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Gender') }}</label>
                <select name="gender" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">{{ __('Select') }}</option>
                    @foreach(['male','female','other'] as $g)
                        <option value="{{ $g }}" @selected(old('gender',$patient->gender)==$g)>{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Blood group') }}</label>
                <select name="blood_group" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">{{ __('Select') }}</option>
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                        <option value="{{ $bg }}" @selected(old('blood_group',$patient->blood_group)==$bg)>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Emergency contact') }}</label>
                <input name="emergency_contact" value="{{ old('emergency_contact',$patient->emergency_contact) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="+91 …">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">{{ __('Aadhaar number (optional)') }}</label>
                <input name="aadhaar_number" value="{{ old('aadhaar_number',$patient->aadhaar_number) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="XXXX XXXX XXXX">
                @error('aadhaar_number')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">{{ __('Allergies') }}</label>
            <textarea name="allergies" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="{{ __('e.g. Penicillin, peanuts') }}">{{ old('allergies',$patient->allergies) }}</textarea>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">{{ __('Medical history') }}</label>
            <textarea name="medical_history" rows="4" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="{{ __('Past surgeries, chronic illnesses, etc.') }}">{{ old('medical_history',$patient->medical_history) }}</textarea>
        </div>
        <div class="flex justify-end">
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">
                <x-icon name="check" class="h-4 w-4"/> {{ __('Save Profile') }}
            </button>
        </div>
    </form>
</div>
@endsection
