@extends('layouts.app')
@section('title', 'Rx '.$prescription->prescription_code)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Prescription') }}</h1>
            <p class="text-slate-500 text-sm">{{ $prescription->prescription_code }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
        <div class="grid sm:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="text-xs uppercase text-slate-500">{{ __('Patient') }}</div>
                <div class="font-bold">{{ $prescription->patient->user->name }}</div>
            </div>
            <div>
                <div class="text-xs uppercase text-slate-500">{{ __('Issued') }}</div>
                <div class="font-bold">{{ $prescription->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        @if($prescription->diagnosis)<div class="mb-4"><span class="font-semibold">{{ __('Diagnosis:') }}</span> {{ $prescription->diagnosis }}</div>@endif
        <table class="w-full text-sm border-collapse">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr><th class="p-3 text-left">Medicine</th><th class="p-3 text-left">Dosage</th><th class="p-3 text-left">Freq</th><th class="p-3 text-left">Duration</th><th class="p-3 text-left">Note</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($prescription->items as $it)
                    <tr>
                        <td class="p-3 font-semibold">{{ $it->medicine_name }}</td>
                        <td class="p-3">{{ $it->dosage }}</td>
                        <td class="p-3">{{ $it->frequency }}</td>
                        <td class="p-3">{{ $it->duration }}</td>
                        <td class="p-3 text-slate-500">{{ $it->instructions }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($prescription->advice)<div class="mt-4"><span class="font-semibold">{{ __('Advice:') }}</span> {{ $prescription->advice }}</div>@endif
    </div>
</div>
@endsection
