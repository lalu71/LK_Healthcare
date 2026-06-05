@extends('layouts.app')
@section('title', __('Prescriptions'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Prescription Requests') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Browse and process prescriptions issued by doctors.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('pharmacist.prescriptions.index')" :q="$q" :placeholder="__('Search patient, doctor, code')" :hasFilters="!empty($q) || !empty($status)">
            <select name="status" class="h-11 rounded-lg border-slate-200 text-sm font-medium text-slate-700 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">{{ __('All status') }}</option>
                <option value="pending" @selected(($status ?? '')==='pending')>{{ __('Pending') }}</option>
                <option value="dispensed" @selected(($status ?? '')==='dispensed')>{{ __('Dispensed') }}</option>
            </select>
        </x-list-filter>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('ID') }}</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('Patient') }}</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('Requested By') }}</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('Medicines') }}</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('Status') }}</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[11px]">{{ __('Date') }}</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-5 font-mono text-[11px] text-emerald-600 font-extrabold">{{ $rx->prescription_code }}</td>
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-900">{{ $rx->patient->user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">ID: {{ $rx->patient->patient_id }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-semibold text-slate-700">Dr. {{ $rx->doctor->user->name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-tighter">{{ $rx->doctor->specialization->name }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                    @foreach($rx->items->take(2) as $item)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-bold text-slate-600 border border-slate-200">{{ $item->medicine_name }}</span>
                                    @endforeach
                                    @if($rx->items->count() > 2)
                                        <span class="text-[10px] text-slate-400 font-bold">+{{ $rx->items->count() - 2 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $rx->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                    {{ __($rx->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-slate-500 font-medium">
                                <div>{{ $rx->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $rx->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <a href="{{ route('pharmacist.prescriptions.pdf', $rx->id) }}"
                                       title="{{ __('Download PDF') }}"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-all shadow-sm">
                                        <x-icon name="download" class="h-4 w-4"/>
                                    </a>
                                    <a href="{{ route('pharmacist.prescriptions.show', $rx->id) }}"
                                       title="{{ __('Open') }}"
                                       class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-all shadow-sm">
                                        <x-icon name="chevron-right" class="h-4 w-4"/>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-16 text-center text-slate-500 font-medium">{{ __('No prescription requests found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($prescriptions->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50">{{ $prescriptions->links() }}</div>
        @endif
    </div>
</div>
@endsection
