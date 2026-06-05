@extends('layouts.app')
@section('title', __('Pharmacy Dashboard'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-600 to-emerald-700 p-8 md:p-12 text-white shadow-xl shadow-teal-900/20 mb-10">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-bold uppercase tracking-widest mb-6 border border-white/30 backdrop-blur-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                    {{ __('Pharmacy Store') }}
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-4">
                    {{ __('Welcome back') }}, {{ auth()->user()->name }}
                </h1>
                <p class="text-teal-50 opacity-90 text-base leading-relaxed">
                    {{ __('Manage medicine inventory and process doctor prescriptions in one place.') }}
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('pharmacist.inventory.index') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-white text-teal-700 font-bold text-sm hover:bg-teal-50 transition-all shadow-xl whitespace-nowrap group">
                    <x-icon name="cart" class="h-5 w-5 group-hover:scale-110 transition-transform"/> {{ __('Manage Inventory') }}
                </a>
            </div>
        </div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="bg-white rounded-2xl border border-slate-200 p-8 flex items-center gap-6 shadow-sm hover:shadow-md transition-all">
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner"><x-icon name="pill" class="h-8 w-8"/></div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Active Medicines') }}</div>
                <div class="text-3xl font-black text-slate-900 leading-none">{{ $stats['total_medicines'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 flex items-center gap-6 shadow-sm hover:shadow-md transition-all">
            <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner"><x-icon name="alert" class="h-8 w-8"/></div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Low Stock') }}</div>
                <div class="text-3xl font-black text-slate-900 leading-none">{{ $stats['low_stock'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 flex items-center gap-6 shadow-sm hover:shadow-md transition-all">
            <div class="h-14 w-14 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center shadow-inner"><x-icon name="file" class="h-8 w-8"/></div>
            <div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Total Requests') }}</div>
                <div class="text-3xl font-black text-slate-900 leading-none">{{ $stats['pending_prescriptions'] }}</div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        {{-- Recent Requests --}}
        <div class="space-y-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                    <h2 class="text-lg font-bold text-slate-900">{{ __('Latest Prescription Requests') }}</h2>
                    <a href="{{ route('pharmacist.prescriptions.index') }}" class="text-teal-600 font-bold text-xs hover:underline flex items-center gap-1">{{ __('View All') }} <x-icon name="chevron-right" class="h-3 w-3"/></a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($latest_prescriptions as $rx)
                        <div class="p-6 flex items-center gap-5 hover:bg-slate-50/50 transition-colors">
                            <div class="h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-400 shrink-0 uppercase text-lg">{{ substr($rx->patient->user->name, 0, 1) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1.5">
                                    <span class="font-extrabold text-slate-900 truncate text-lg">{{ $rx->patient->user->name }}</span>
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-[10px] font-mono font-bold text-slate-400 shrink-0 border border-slate-200">{{ $rx->prescription_code }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <span class="flex items-center gap-1.5 font-semibold"><x-icon name="stethoscope" class="h-4 w-4 text-emerald-500"/> Dr. {{ $rx->doctor->user->name }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1.5 font-medium"><x-icon name="clock" class="h-4 w-4 text-slate-400"/> {{ $rx->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <a href="{{ route('pharmacist.prescriptions.show', $rx->id) }}" class="h-12 px-6 rounded-2xl bg-teal-600 text-white text-xs font-bold flex items-center justify-center hover:bg-teal-700 shadow-lg shadow-teal-600/20 transition-all shrink-0">
                                {{ __('Process') }}
                            </a>
                        </div>
                    @empty
                        <div class="p-20 text-center text-slate-400 font-medium">
                            <x-icon name="file" class="h-12 w-12 mx-auto mb-4 opacity-20"/>
                            {{ __('No prescriptions found.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
