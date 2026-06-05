@extends('layouts.app')
@section('title', __('Doctor Dashboard'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <div class="rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-500 p-6 sm:p-8 text-white">
        <p class="text-emerald-100 text-sm">{{ __('Welcome back') }}</p>
        <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold">Dr. {{ auth()->user()->name }} 👨‍⚕️</h1>
        @if($doctor && $doctor->specialization)
            <div class="mt-2 text-emerald-100 text-sm">{{ __($doctor->specialization->name) }} · {{ $doctor->experience_years }} {{ __('yrs experience') }}</div>
        @endif
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>__("Today's"),'value'=>$stats['today'],'icon'=>'calendar','color'=>'teal'],
            ['label'=>__('Upcoming'),'value'=>$stats['upcoming'],'icon'=>'clock','color'=>'emerald'],
            ['label'=>__('Total Appts.'),'value'=>$stats['total'],'icon'=>'chart','color'=>'violet'],
            ['label'=>__('Unique Patients'),'value'=>$stats['patients'],'icon'=>'users','color'=>'amber'],
        ] as $s)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
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

    @if(!$doctor)
        <div class="rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 flex items-center gap-4">
            <x-icon name="alert" class="h-6 w-6 text-amber-600 shrink-0"/>
            <div class="flex-1">
                <div class="font-bold">{{ __('Complete your doctor profile') }}</div>
                <div class="text-sm">{{ __('Add specialization and fees to start accepting appointments.') }}</div>
            </div>
            <a href="{{ route('doctor.profile.edit') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white font-semibold text-sm hover:bg-amber-700">{{ __('Set up') }}</a>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">{{ __('Upcoming Appointments') }}</h3>
            <a href="{{ route('doctor.appointments.index') }}" class="text-sm text-teal-600 font-semibold hover:underline">{{ __('View all') }} →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($upcoming as $apt)
                <div class="p-5 flex items-center gap-4 hover:bg-slate-50">
                    <div class="h-12 w-12 rounded-xl bg-teal-50 text-teal-600 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold uppercase">{{ $apt->appointment_date->translatedFormat('M') }}</span>
                        <span class="text-lg font-extrabold leading-none">{{ $apt->appointment_date->translatedFormat('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-slate-900">{{ $apt->patient->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $apt->appointment_date->translatedFormat('h:i A') }} · {{ $apt->reason ?: __('General consultation') }}</div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                        {{ $apt->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                        {{ $apt->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                        {{ __($apt->status) }}
                    </span>
                    <a href="{{ route('doctor.appointments.show',$apt->id) }}" class="text-teal-600 font-semibold text-xs hover:underline">{{ __('Open') }} →</a>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <x-icon name="calendar" class="h-12 w-12 mx-auto opacity-50"/>
                    <p class="mt-3 text-sm">{{ __('No upcoming appointments.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
