<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') · {{ config('app.name', 'LK Healthcare') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/site_images/lklogo.png') }}?v=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-100">
<div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-teal-600 via-teal-500 to-emerald-500 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image:radial-gradient(circle at 20% 20%, white 1px, transparent 1px), radial-gradient(circle at 80% 70%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="inline-flex h-14 w-14 rounded-xl bg-white items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/site_images/lklogo.png') }}" alt="LK Healthcare Logo" class="max-h-full max-w-full object-contain">
                </span>
                <span class="font-extrabold tracking-tight text-2xl">Healthcare</span>
            </a>
            <div>
                <h1 class="text-4xl font-extrabold leading-tight mb-4">{{ __('Your Health, Our Priority.') }}</h1>
                <p class="text-teal-100 text-lg max-w-md">{{ __('Book appointments, access records, order medicines, and connect with top doctors — all in one secure platform.') }}</p>
                <div class="mt-10 grid grid-cols-2 gap-4 max-w-md">
                    <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/20">
                        <div class="text-2xl font-extrabold">500+</div>
                        <div class="text-teal-100 text-xs">{{ __('Specialist Doctors') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/20">
                        <div class="text-2xl font-extrabold">24/7</div>
                        <div class="text-teal-100 text-xs">{{ __('Emergency Support') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/20">
                        <div class="text-2xl font-extrabold">100K+</div>
                        <div class="text-teal-100 text-xs">{{ __('Happy Patients') }}</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/20">
                        <div class="text-2xl font-extrabold">15+</div>
                        <div class="text-teal-100 text-xs">{{ __('Specialities') }}</div>
                    </div>
                </div>
            </div>
            <div class="text-teal-100 text-sm">© {{ date('Y') }} LK Healthcare · {{ __('All rights reserved') }}</div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-8 flex items-center justify-center gap-3">
                <span class="inline-flex h-14 w-14 rounded-xl bg-white border border-slate-200 items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/site_images/lklogo.png') }}" alt="LK Healthcare Logo" class="max-h-full max-w-full object-contain">
                </span>
                <span class="font-extrabold text-2xl text-slate-900">LK <span class="text-teal-600">Healthcare</span></span>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
