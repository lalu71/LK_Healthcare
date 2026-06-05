@extends('layouts.app')
@section('title', __('Availability'))
@section('content')
@php
    $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    $takenDays = $availabilities->pluck('day_of_week')->map(fn($d) => (int) $d)->all();
    $allDaysTaken = count(array_unique($takenDays)) >= 7;
@endphp
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="clock" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Availability') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Set weekly slots when you are available for consultations.') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[360px_1fr] gap-6">
        @if($allDaysTaken)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
                <h3 class="font-bold text-slate-900">{{ __('Add Slot') }}</h3>
                <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm p-3">
                    {{ __('You have set availability for all 7 days. Use the update option on the right to change a day.') }}
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('doctor.availability.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 h-fit">
                @csrf
                <h3 class="font-bold text-slate-900">{{ __('Add Slot') }}</h3>
                <div>
                    <label class="text-sm font-semibold text-slate-700">{{ __('Day') }}</label>
                    <select name="day_of_week" class="mt-1 w-full rounded-lg border-slate-300">
                        @foreach($days as $i => $d)
                            @if(!in_array($i, $takenDays, true))
                                <option value="{{ $i }}" @selected(old('day_of_week') == $i)>{{ __($d) }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('day_of_week')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('Start') }}</label>
                        <input type="time" name="start_time" required class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">{{ __('End') }}</label>
                        <input type="time" name="end_time" required class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">{{ __('Slot length') }}</label>
                    <select name="slot_minutes" class="mt-1 w-full rounded-lg border-slate-300">
                        <option value="15">15 {{ __('min') }}</option>
                        <option value="20">20 {{ __('min') }}</option>
                        <option value="30" selected>30 {{ __('min') }}</option>
                        <option value="45">45 {{ __('min') }}</option>
                        <option value="60">60 {{ __('min') }}</option>
                    </select>
                </div>
                <button class="w-full px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">{{ __('Add') }}</button>
            </form>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <h3 class="font-bold text-slate-900">{{ __('Your Weekly Schedule') }}</h3>
            </div>
            @if($availabilities->isEmpty())
                <div class="p-12 text-center text-slate-400"><x-icon name="clock" class="h-12 w-12 mx-auto text-slate-300"/><p class="mt-3">{{ __('No slots yet.') }}</p></div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($availabilities as $a)
                        <div x-data="{ editing: false }" class="p-4">
                            <div x-show="!editing" class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ __(['SUN','MON','TUE','WED','THU','FRI','SAT'][$a->day_of_week]) }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($a->start_time)->translatedFormat('h:i A') }} – {{ \Carbon\Carbon::parse($a->end_time)->translatedFormat('h:i A') }}</div>
                                    <div class="text-xs text-slate-500">{{ $a->slot_minutes }} {{ __('min slots') }}</div>
                                </div>
                                <button type="button" @click="editing = true" class="text-emerald-600 hover:bg-emerald-50 p-2 rounded-lg" title="{{ __('Update') }}">
                                    <x-icon name="edit" class="h-4 w-4"/>
                                </button>
                                <form method="POST" action="{{ route('doctor.availability.destroy',$a->id) }}" onsubmit="return confirm('{{ __('Remove this slot?') }}')">@csrf @method('DELETE')
                                    <button class="text-rose-500 hover:bg-rose-50 p-2 rounded-lg" title="{{ __('Delete') }}"><x-icon name="trash" class="h-4 w-4"/></button>
                                </form>
                            </div>

                            <form x-show="editing" x-cloak method="POST" action="{{ route('doctor.availability.update', $a->id) }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                                @csrf @method('PATCH')
                                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ __(['SUN','MON','TUE','WED','THU','FRI','SAT'][$a->day_of_week]) }}
                                </div>
                                <div class="flex-1 grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">{{ __('Start') }}</label>
                                        <input type="time" name="start_time" required value="{{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">{{ __('End') }}</label>
                                        <input type="time" name="end_time" required value="{{ \Carbon\Carbon::parse($a->end_time)->format('H:i') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600">{{ __('Slot') }}</label>
                                        <select name="slot_minutes" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                                            @foreach([15,20,30,45,60] as $m)
                                                <option value="{{ $m }}" @selected($a->slot_minutes == $m)>{{ $m }} {{ __('min') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">{{ __('Save') }}</button>
                                    <button type="button" @click="editing = false" class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">{{ __('Cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
