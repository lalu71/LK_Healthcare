@extends('layouts.app')
@section('title', __('Doctor Profile'))
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="stethoscope" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Professional Profile') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Complete your profile to start accepting appointments.') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('doctor.profile.update') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Specialization') }}</label>
                <select name="specialization_id" required class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    <option value="">{{ __('Select') }}</option>
                    @foreach($specializations as $sp)
                        <option value="{{ $sp->id }}" @selected(old('specialization_id',$doctor->specialization_id)==$sp->id)>{{ $sp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Experience (years)') }}</label>
                <input type="number" min="0" max="60" name="experience_years" value="{{ old('experience_years',$doctor->experience_years) }}" required class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Consultation fee (INR)') }}</label>
                <input type="number" step="0.01" min="0" name="consultation_fee" value="{{ old('consultation_fee',$doctor->consultation_fee) }}" required class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Qualification') }}</label>
                <input name="qualification" value="{{ old('qualification',$doctor->qualification) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="MBBS, MD">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">{{ __('Clinic address') }}</label>
                <input name="clinic_address" value="{{ old('clinic_address',$doctor->clinic_address) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-semibold text-slate-700">{{ __('Short bio') }}</label>
                <textarea name="bio" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">{{ old('bio',$doctor->bio) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end">
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">
                <x-icon name="check" class="h-4 w-4"/> {{ __('Save Profile') }}
            </button>
        </div>
    </form>
</div>
@endsection
