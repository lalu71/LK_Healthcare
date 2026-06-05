@extends('layouts.app')
@section('title', 'Rx '.$prescription->prescription_code)
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Prescription') }}</h1>
                <p class="text-slate-500 text-sm">{{ $prescription->prescription_code }} · {{ $prescription->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        <a href="{{ route('patient.prescriptions.pdf',$prescription->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700"><x-icon name="download" class="h-5 w-5"/> {{ __('Download PDF') }}</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="text-xs text-slate-500 uppercase tracking-wider">{{ __('Doctor') }}</div>
                <div class="font-bold text-slate-900 text-lg">Dr. {{ $prescription->doctor->user->name }}</div>
                <div class="text-sm text-slate-500">{{ $prescription->doctor->specialization->name ?? '-' }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-500 uppercase tracking-wider">{{ __('Patient') }}</div>
                <div class="font-bold text-slate-900 text-lg">{{ $prescription->patient->user->name }}</div>
                <div class="text-sm text-slate-500">ID: {{ $prescription->patient->patient_id ?? '-' }}</div>
            </div>
        </div>

        @if($prescription->diagnosis)
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Diagnosis') }}</div>
                <p class="text-slate-800">{{ $prescription->diagnosis }}</p>
            </div>
        @endif

        <div>
            <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Rx · {{ __('Medicines') }}</div>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2.5 text-left">{{ __('Medicine') }}</th>
                            <th class="px-4 py-2.5 text-left">{{ __('Dosage') }}</th>
                            <th class="px-4 py-2.5 text-left">{{ __('Frequency') }}</th>
                            <th class="px-4 py-2.5 text-left">{{ __('Duration') }}</th>
                            <th class="px-4 py-2.5 text-left">{{ __('Note') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($prescription->items as $it)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $it->medicine_name }}</td>
                                <td class="px-4 py-3">{{ $it->dosage }}</td>
                                <td class="px-4 py-3">{{ $it->frequency }}</td>
                                <td class="px-4 py-3">{{ $it->duration }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $it->instructions }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($prescription->advice)
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Advice') }}</div>
                <p class="text-slate-800">{{ $prescription->advice }}</p>
            </div>
        @endif

        @if($prescription->follow_up_date)
            <div class="rounded-xl bg-teal-50 border border-teal-100 px-4 py-3 text-sm text-teal-800">
                <strong>{{ __('Follow-up:') }}</strong> {{ $prescription->follow_up_date->format('d M Y') }}
            </div>
        @endif
    </div>
</div>
@endsection
