<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'LK Healthcare') }}</title>

    <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
     <link rel="icon" type="image/png" href="{{ asset('assets/site_images/lklogo.png') }}?v=1">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak]{display:none!important}
        .sidebar-link.active{ background: linear-gradient(90deg, rgba(13,148,136,.12), rgba(13,148,136,0)); color:#0f766e; border-left:3px solid #14b8a6; }
        /* Card shadow for subtle lift against the page background */
        .bg-white.border.border-slate-200{ box-shadow: 0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.04); }
        .bg-white.border.border-slate-200:hover{ box-shadow: 0 4px 6px -1px rgb(15 23 42 / 0.07), 0 2px 4px -2px rgb(15 23 42 / 0.05); }
    </style>
</head>
<body class="font-sans antialiased bg-slate-100 text-slate-800">
<div x-data="{ sidebar: false }" class="min-h-screen flex">

    {{-- SIDEBAR --}}
    @include('layouts.partials.sidebar')

    {{-- OVERLAY (mobile) --}}
    <div x-show="sidebar" x-transition.opacity @click="sidebar=false" class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden" x-cloak></div>

    {{-- MAIN --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

        @include('layouts.partials.topbar')

        {{-- page heading --}}
        @hasSection('header')
            <div class="bg-white border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    @yield('header')
                </div>
            </div>
        @endif

        {{-- Read-only banner: site is shut down, non-admins can browse but not act --}}
        @if(\App\Models\Setting::isShutdown() && (! auth()->user() || ! auth()->user()->hasRole('admin')))
            <div class="bg-rose-50 border-b border-rose-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-3 text-rose-800 text-sm">
                    <x-icon name="alert" class="h-5 w-5 shrink-0"/>
                    <span class="font-bold">{{ __('Site is shut down.') }}</span>
                    <span class="font-semibold">{{ __('View only — actions are disabled.') }}</span>
                </div>
            </div>
        @endif

        {{-- flash --}}
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-2">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <x-icon name="check" class="h-5 w-5 text-emerald-600"/>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <x-icon name="alert" class="h-5 w-5 text-rose-600"/>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
            @if($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 font-semibold mb-1"><x-icon name="alert" class="h-5 w-5"/> {{ __('Please fix the errors below') }}</div>
                        <ul class="list-disc ml-8 text-sm">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-rose-500 hover:text-rose-700 transition-colors mt-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
        </div>

        <main class="flex-1">
            @isset($slot){{ $slot }}@endisset
            @yield('content')
        </main>

        @include('layouts.partials.footer-sm')
    </div>

    @include('layouts.partials.chatbot')
</div>
</body>
</html>
