@extends('layouts.public')
@section('title', __('Contact Us'))
@section('content')
<section class="py-14 bg-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">{{ __('Get in touch') }}</h1>
        <p class="mt-3 text-slate-600 max-w-xl">{{ __('Questions, feedback, or partnership? We would love to hear from you.') }}</p>
    </div>
</section>

<section class="py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-10">
        <div class="lg:col-span-1 space-y-5">
            <div class="p-5 rounded-2xl border border-slate-200 bg-white flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><x-icon name="phone" class="h-5 w-5"/></div>
                <div>
                    <div class="text-sm text-slate-500">{{ __('Call us') }}</div>
                    <div class="font-bold text-slate-900">+91 {{ $siteContent->help_contact ?? '8303224404' }}</div>
                </div>
            </div>
            <div class="p-5 rounded-2xl border border-slate-200 bg-white flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><x-icon name="mail" class="h-5 w-5"/></div>
                <div>
                    <div class="text-sm text-slate-500">{{ __('Email') }}</div>
                    <div class="font-bold text-slate-900">{{ $siteContent->site_email ?? 'lalje056@gmail.com' }}</div>
                </div>
            </div>
            <div class="p-5 rounded-2xl border border-slate-200 bg-white flex items-start gap-4">
                <div class="h-10 w-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><x-icon name="location" class="h-5 w-5"/></div>
                <div>
                    <div class="text-sm text-slate-500">{{ __('Head office') }}</div>
                    <div class="font-bold text-slate-900">{{ $siteContent->site_address ?? 'Noida Sector 16, Uttar Pradesh - 201301' }}</div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('public.contact.store') }}" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('Name') }}</label>
                        <input name="name" required value="{{ old('name') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
                        <input name="email" type="email" required value="{{ old('email') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('Phone') }}</label>
                        <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('Subject') }}</label>
                        <input name="subject" value="{{ old('subject') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">{{ __('Message') }}</label>
                    <textarea name="message" required rows="5" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">{{ old('message') }}</textarea>
                </div>
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2"><x-icon name="check" class="h-4 w-4"/> {{ session('success') }}</div>
                        <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif
                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2"><x-icon name="alert" class="h-4 w-4"/> {{ $errors->first() }}</div>
                        <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif
                <button class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700">
                    <x-icon name="mail" class="h-5 w-5"/> {{ __('Send Message') }}
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
