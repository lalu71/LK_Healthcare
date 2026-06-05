@extends('layouts.app')
@section('title', __('Patient Dashboard'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Welcome --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-600 via-teal-500 to-emerald-500 p-6 sm:p-8 text-white">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 20% 20%, white 1.5px, transparent 1.5px); background-size:28px 28px;"></div>
        <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-4 justify-between">
            <div>
                <p class="text-teal-100 text-sm">{{ __('Welcome back') }}</p>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold">{{ auth()->user()->name }} 👋</h1>
                @if($patient && $patient->patient_id)
                    <span class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/20 backdrop-blur border border-white/20">
                        ID · {{ $patient->patient_id }}
                    </span>
                @endif
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('patient.book') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-teal-600 font-semibold hover:bg-teal-50">
                    <x-icon name="calendar" class="h-5 w-5"/> {{ __('Book Appointment') }}
                </a>
                <a href="{{ route('emergency.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 text-slate-900 font-semibold hover:bg-amber-400">
                    <x-icon name="ambulance" class="h-5 w-5"/> {{ __('Emergency') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>__('Upcoming'),'value'=>$stats['upcoming'],'icon'=>'calendar','color'=>'teal'],
            ['label'=>__('Completed'),'value'=>$stats['completed'],'icon'=>'check','color'=>'emerald'],
            ['label'=>__('Prescriptions'),'value'=>$stats['prescriptions'],'icon'=>'pill','color'=>'violet'],
            ['label'=>__('Records'),'value'=>$stats['records'],'icon'=>'file','color'=>'amber'],
        ] as $s)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-{{ $s['color'] }}-50 text-{{ $s['color'] }}-600 flex items-center justify-center">
                        <x-icon :name="$s['icon']" class="h-5 w-5"/>
                    </div>
                    <div>
                        <div class="text-xs text-slate-500">{{ $s['label'] }}</div>
                        <div class="text-2xl font-extrabold text-slate-900">{{ $s['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['r'=>route('patient.lab.index'),'i'=>'flask','t'=>__('Lab Tests'),'c'=>'violet'],
            ['r'=>route('patient.pharmacy.index'),'i'=>'pill','t'=>__('Pharmacy'),'c'=>'emerald'],
            ['r'=>route('patient.records.index'),'i'=>'upload','t'=>__('Upload Report'),'c'=>'amber'],
            ['r'=>route('blood.index'),'i'=>'droplet','t'=>__('Blood Bank'),'c'=>'rose'],
        ] as $q)
            <a href="{{ $q['r'] }}" class="group bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-lg hover:-translate-y-0.5 transition text-center">
                <div class="h-12 w-12 mx-auto rounded-xl bg-{{ $q['c'] }}-50 text-{{ $q['c'] }}-600 flex items-center justify-center group-hover:bg-{{ $q['c'] }}-600 group-hover:text-white transition">
                    <x-icon :name="$q['i']" class="h-6 w-6"/>
                </div>
                <div class="mt-3 font-semibold text-slate-800 text-sm">{{ $q['t'] }}</div>
            </a>
        @endforeach
    </div>

    {{-- Upcoming & Prescriptions --}}
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">{{ __('Upcoming Appointments') }}</h3>
                <a href="{{ route('patient.appointments.index') }}" class="text-sm text-teal-600 font-semibold hover:underline">{{ __('View all') }} →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($upcoming as $apt)
                    <div class="p-5 flex items-center gap-4 hover:bg-slate-50">
                        <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-600 flex flex-col items-center justify-center shrink-0">
                            <span class="text-[10px] font-bold uppercase">{{ $apt->appointment_date->translatedFormat('M') }}</span>
                            <span class="text-lg font-extrabold leading-none">{{ $apt->appointment_date->translatedFormat('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900">Dr. {{ $apt->doctor->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ __($apt->doctor->specialization->name ?? '-') }} · {{ $apt->appointment_date->translatedFormat('h:i A') }}</div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                            {{ $apt->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $apt->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                            {{ __($apt->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400">
                        <x-icon name="calendar" class="h-12 w-12 mx-auto opacity-50"/>
                        <p class="mt-3 text-sm">{{ __('No upcoming appointments.') }}</p>
                        <a href="{{ route('patient.book') }}" class="mt-3 inline-flex items-center gap-2 text-teal-600 font-semibold">{{ __('Book one now') }} →</a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800">{{ __('Recent Prescriptions') }}</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentPrescriptions as $rx)
                    <a href="{{ route('patient.prescriptions.show', $rx->id) }}" class="block p-5 hover:bg-slate-50">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                                <x-icon name="pill" class="h-5 w-5"/>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-900 text-sm">{{ $rx->prescription_code }}</div>
                                <div class="text-xs text-slate-500">Dr. {{ $rx->doctor->user->name }} · {{ $rx->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">{{ __('No prescriptions yet.') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    @if(!$patient)
        <div class="rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 flex items-center gap-4">
            <x-icon name="alert" class="h-6 w-6 text-amber-600 shrink-0"/>
            <div class="flex-1">
                <div class="font-bold">{{ __('Complete your medical profile') }}</div>
                <div class="text-sm">{{ __('Add blood group, allergies and emergency contact to book faster.') }}</div>
            </div>
            <a href="{{ route('patient.profile.edit') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white font-semibold hover:bg-amber-700 text-sm">{{ __('Complete') }}</a>
        </div>
    @endif
</div>
@endsection
