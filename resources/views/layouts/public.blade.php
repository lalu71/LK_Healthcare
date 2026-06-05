<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') · {{ config('app.name', 'LK Healthcare') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/site_images/lklogo.png') }}?v=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important}
        .bg-white.border.border-slate-200{ box-shadow: 0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.04); }
    </style>
</head>
<body class="font-sans antialiased text-slate-800 bg-white">

<header x-data="{ open:false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="w-full px-4 sm:px-6 lg:px-10 h-16 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <span class="inline-flex h-14 w-14 items-center justify-center overflow-hidden"><img src="{{ asset('assets/site_images/lklogo.png') }}"  alt="LK Healthcare Logo"  class="max-h-full max-w-full object-contain"></span>
            <span class="font-extrabold tracking-tight text-slate-900 text-xl">Healthcare</span>
        </a>
        <nav class="hidden lg:flex items-center gap-8 text-sm font-medium text-slate-700">
            <a href="{{ url('/') }}" class="hover:text-teal-600 {{ request()->is('/') ? 'text-teal-600' : '' }}">{{ __('Home') }}</a>
            <a href="{{ route('public.about') }}" class="hover:text-teal-600 {{ request()->routeIs('public.about') ? 'text-teal-600' : '' }}">{{ __('About') }}</a>
            <a href="{{ route('public.services') }}" class="hover:text-teal-600 {{ request()->routeIs('public.services') ? 'text-teal-600' : '' }}">{{ __('Services') }}</a>
            <a href="{{ route('public.doctors') }}" class="hover:text-teal-600 {{ request()->routeIs('public.doctors') ? 'text-teal-600' : '' }}">{{ __('Doctors') }}</a>
            <a href="{{ route('public.contact') }}" class="hover:text-teal-600 {{ request()->routeIs('public.contact') ? 'text-teal-600' : '' }}">{{ __('Contact') }}</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('lang.switch', app()->getLocale()=='en' ? 'hi' : 'en') }}" class="hidden sm:inline-flex items-center gap-1 text-sm text-slate-600 hover:text-teal-600 px-2 py-1.5 rounded">
                <x-icon name="globe" class="h-4 w-4"/>
                <span class="uppercase">{{ app()->getLocale()=='en' ? 'हि' : 'EN' }}</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">{{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-sm font-semibold text-slate-700 hover:text-teal-600">{{ __('Log in') }}</a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">{{ __('Get Started') }}</a>
            @endauth
            <button @click="open=!open" class="lg:hidden p-2 rounded hover:bg-slate-100 text-slate-600"><x-icon name="menu" class="h-6 w-6"/></button>
            <a href="{{ route('emergency.create') }}" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-bold hover:bg-rose-700 shadow-sm animate-pulse">
                <x-icon name="ambulance" class="h-4 w-4"/>
                <span class="hidden sm:inline">{{ __('Emergency') }}</span>
            </a>
        </div>
    </div>
    <div x-show="open" x-cloak class="lg:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 flex flex-col gap-1">
            <a href="{{ url('/') }}" class="py-2 text-sm">{{ __('Home') }}</a>
            <a href="{{ route('public.about') }}" class="py-2 text-sm">{{ __('About') }}</a>
            <a href="{{ route('public.services') }}" class="py-2 text-sm">{{ __('Services') }}</a>
            <a href="{{ route('public.doctors') }}" class="py-2 text-sm">{{ __('Doctors') }}</a>
            <a href="{{ route('public.contact') }}" class="py-2 text-sm">{{ __('Contact') }}</a>
            <a href="{{ route('emergency.create') }}" class="mt-1 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-rose-600 text-white text-sm font-bold hover:bg-rose-700">
                <x-icon name="ambulance" class="h-4 w-4"/> {{ __('Emergency') }}
            </a>
        </div>
    </div>
</header>

<main>{{ $slot ?? '' }}@yield('content')</main>

<footer class="bg-slate-900 text-slate-300 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid md:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center gap-2 mb-4">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <span class="inline-flex h-14 w-14 items-center justify-center overflow-hidden"><img src="{{ asset('assets/site_images/lklogo.png') }}"  alt="LK Healthcare Logo"  class="max-h-full max-w-full object-contain"></span>
                    <span class="font-extrabold tracking-tight text-white-900 text-xl">Healthcare</span>
                </a>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed">{{ __('Trusted healthcare platform connecting patients, doctors, and hospitals across India with secure records and 24/7 support.') }}</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Quick Links') }}</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('public.about') }}" class="hover:text-teal-400">{{ __('About Us') }}</a></li>
                <li><a href="{{ route('public.services') }}" class="hover:text-teal-400">{{ __('Services') }}</a></li>
                <li><a href="{{ route('public.doctors') }}" class="hover:text-teal-400">{{ __('Our Doctors') }}</a></li>
                <li><a href="{{ route('public.contact') }}" class="hover:text-teal-400">{{ __('Contact') }}</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Services') }}</h4>
            <ul class="space-y-2 text-sm">
                <li>{{ __('Online Consultation') }}</li>
                <li>{{ __('Lab Tests') }}</li>
                <li>{{ __('Pharmacy') }}</li>
                <li>{{ __('Emergency & Ambulance') }}</li>
                <li>{{ __('Blood Bank') }}</li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">{{ __('Contact') }}</h4>
            <ul class="space-y-2 text-sm">
                <li class="flex items-center gap-2"><x-icon name="phone" class="h-4 w-4"/> +91 1800-LK-HEALTH</li>
                <li class="flex items-center gap-2"><x-icon name="mail" class="h-4 w-4"/> contact@lkhealthcare.in</li>
                <li class="flex items-center gap-2"><x-icon name="location" class="h-4 w-4"/> {{ __('Delhi') }} · {{ __('Mumbai') }} · {{ __('Bangalore') }}</li>
            </ul>
        </div>
    </div>
</footer>

</body>
</html>
