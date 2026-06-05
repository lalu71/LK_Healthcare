@extends('layouts.app')
@section('title', __('Add Doctor'))
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-extrabold text-slate-900 mb-6">{{ __('Add New Doctor') }}</h1>
    <form method="POST" action="{{ route('admin.doctors.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5">
        @csrf
        <div class="grid sm:grid-cols-2 gap-5">
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Full name') }}</label><input name="name" required class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Email') }}</label><input type="email" name="email" required class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Phone') }}</label><input name="phone" class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Password') }}</label><input type="text" name="password" required class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div class="sm:col-span-2"><label class="text-sm font-semibold text-slate-700">{{ __('Profile Picture') }}</label><input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="mt-1 w-full rounded-lg border-slate-300 py-1.5 text-xs"><p class="text-[11px] text-slate-400 mt-1">{{ __('JPG, PNG, or WebP up to 2 MB') }}</p>@error('avatar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Specialization') }}</label>
                <select name="specialization_id" required class="mt-1 w-full rounded-lg border-slate-300">
                    <option value="">{{ __('Select') }}</option>
                    @foreach($specializations as $sp)<option value="{{ $sp->id }}">{{ $sp->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Qualification') }}</label><input name="qualification" class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Experience (yrs)') }}</label><input type="number" name="experience_years" required min="0" class="mt-1 w-full rounded-lg border-slate-300"></div>
            <div><label class="text-sm font-semibold text-slate-700">{{ __('Consultation fee') }}</label><input type="number" step="0.01" name="consultation_fee" required class="mt-1 w-full rounded-lg border-slate-300"></div>
        </div>
        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700"><x-icon name="plus" class="h-5 w-5"/> {{ __('Create Doctor') }}</button>
    </form>
</div>
@endsection
