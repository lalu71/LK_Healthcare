@extends('layouts.app')
@section('title', __('Prescriptions'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('My Prescriptions') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Digital prescriptions written by your doctors.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('patient.prescriptions.index')" :q="$q" :placeholder="__('Search doctor, code, diagnosis')" :hasFilters="!empty($q)" />
    </div>

    @if($prescriptions->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <x-icon name="pill" class="h-14 w-14 mx-auto text-slate-300"/>
            <p class="mt-3 text-slate-500">{{ __('No prescriptions found.') }}</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($prescriptions as $rx)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-wider uppercase text-violet-600">
                                <x-icon name="pill" class="h-3.5 w-3.5"/> {{ $rx->prescription_code }}
                            </div>
                            <h3 class="mt-1 font-bold text-slate-900">Dr. {{ $rx->doctor->user->name }}</h3>
                            <div class="text-xs text-slate-500">{{ __($rx->doctor->specialization->name ?? '-') }} · {{ $rx->created_at->translatedFormat('d M Y') }}</div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('patient.prescriptions.show',$rx->id) }}" class="text-xs font-semibold text-teal-600 hover:underline inline-flex items-center gap-1"><x-icon name="eye" class="h-4 w-4"/> {{ __('View') }}</a>
                            <a href="{{ route('patient.prescriptions.pdf',$rx->id) }}" class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1"><x-icon name="download" class="h-4 w-4"/> {{ __('PDF') }}</a>
                        </div>
                    </div>
                    @if($rx->diagnosis)
                        <div class="mt-3 text-sm text-slate-600"><span class="font-semibold text-slate-800">{{ __('Diagnosis:') }}</span> {{ $rx->diagnosis }}</div>
                    @endif
                    <div class="mt-3 text-xs text-slate-500">{{ $rx->items->count() }} {{ __('medicines') }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $prescriptions->links() }}</div>
    @endif
</div>
@endsection
