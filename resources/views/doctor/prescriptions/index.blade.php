@extends('layouts.app')
@section('title', __('Prescriptions'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Prescriptions') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('All prescriptions you have issued.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('doctor.prescriptions.index')" :q="$q" :placeholder="__('Search patient, code, diagnosis')" :hasFilters="!empty($q)" />
    </div>

    @if($prescriptions->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center"><x-icon name="pill" class="h-14 w-14 mx-auto text-slate-300"/><p class="mt-3 text-slate-500">{{ __('No prescriptions found.') }}</p></div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Code</th>
                        <th class="px-5 py-3 text-left">{{ __('Patient') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Diagnosis') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Items') }}</th>
                        <th class="px-5 py-3 text-left">{{ __('Issued') }}</th>
                        <th class="px-5 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($prescriptions as $rx)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-3 font-mono text-xs">{{ $rx->prescription_code }}</td>
                            <td class="px-5 py-3 font-semibold">{{ $rx->patient->user->name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ Str::limit($rx->diagnosis, 40) ?: '—' }}</td>
                            <td class="px-5 py-3">{{ $rx->items->count() }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $rx->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('doctor.prescriptions.show',$rx->id) }}" class="text-teal-600 font-semibold text-xs hover:underline">{{ __('View') }} →</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-50">{{ $prescriptions->links() }}</div>
        </div>
    @endif
</div>
@endsection
