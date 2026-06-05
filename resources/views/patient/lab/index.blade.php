@extends('layouts.app')
@section('title', __('Lab Tests'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="flask" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Lab Tests') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Book certified tests. Digital reports in 24 hours.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('patient.lab.index')" :q="$q" :placeholder="__('Search test or category')" :hasFilters="!empty($q)" />
    </div>

    @if($tests->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center mb-6">
            <x-icon name="flask" class="h-14 w-14 mx-auto text-slate-300"/>
            <p class="mt-3 text-slate-500">{{ __('No tests found.') }}</p>
        </div>
    @endif

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tests as $test)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 flex flex-col">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><x-icon name="flask" class="h-5 w-5"/></div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-slate-900">{{ __($test->name) }}</div>
                        @if($test->category)<div class="text-xs text-slate-500">{{ __($test->category) }}</div>@endif
                    </div>
                </div>
                @if($test->description)<p class="mt-3 text-sm text-slate-600 line-clamp-2">{{ $test->description }}</p>@endif
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-500">{{ __('Price') }}</div>
                        <div class="font-extrabold text-slate-900">₹{{ number_format($test->price,0) }}</div>
                    </div>
                    <form method="POST" action="{{ route('patient.lab.book') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="lab_test_id" value="{{ $test->id }}">
                        <input type="date" name="booking_date" required min="{{ now()->format('Y-m-d') }}" value="{{ now()->addDay()->format('Y-m-d') }}" class="rounded-lg border-slate-300 text-xs py-1.5">
                        <button class="px-3 py-2 rounded-lg bg-teal-600 text-white text-xs font-bold hover:bg-teal-700">{{ __('Book') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @if($bookings->isNotEmpty())
        <div class="mt-10">
            <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('My Bookings') }}</h2>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Code') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Test') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Date') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Amount') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-5 py-3 text-left">{{ __('Result') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($bookings as $b)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $b->booking_code }}</td>
                                <td class="px-5 py-3">{{ __($b->labTest->name) }}</td>
                                <td class="px-5 py-3">{{ $b->booking_date->translatedFormat('d M Y, h:i A') }}</td>
                                <td class="px-5 py-3">₹{{ number_format($b->amount,0) }}</td>
                                <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-teal-100 text-teal-700">{{ __($b->status) }}</span></td>
                                <td class="px-5 py-3">
                                    @if($b->result_file)
                                        <a href="{{ asset('storage/'.$b->result_file) }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:underline inline-flex items-center gap-1"><x-icon name="download" class="h-4 w-4"/> {{ __('View') }}</a>
                                    @else
                                        <span class="text-xs text-slate-400">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </div>
    @endif
</div>
@endsection
