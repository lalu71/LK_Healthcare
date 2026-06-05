@extends('layouts.app')
@section('title', __('Inventory Management'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <x-icon name="cart" class="h-5 w-5"/>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-extrabold text-slate-900 whitespace-nowrap">{{ __('Medicine Inventory') }}</h1>
                <p class="text-slate-500 text-xs truncate">{{ __('Monitor and update stock levels') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('pharmacist.inventory.index')" :q="$q" :placeholder="__('Search medicines...')" :hasFilters="!empty($q) || !empty($filter)">
            <select name="filter" class="h-11 rounded-lg border-slate-200 text-sm font-medium text-slate-700 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">{{ __('All stock') }}</option>
                <option value="low" @selected(($filter ?? '')==='low')>{{ __('Low stock') }}</option>
                <option value="critical" @selected(($filter ?? '')==='critical')>{{ __('Critical') }}</option>
            </select>
        </x-list-filter>
    </div>

    {{-- Stat strip (clickable filters) --}}
    @php
        $allUrl = request()->fullUrlWithQuery(['filter' => null]);
        $lowUrl = request()->fullUrlWithQuery(['filter' => 'low']);
        $critUrl = request()->fullUrlWithQuery(['filter' => 'critical']);
    @endphp
    @php
        $activeCheck = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
    @endphp
    <div class="grid grid-cols-3 gap-3">
        <a href="{{ $allUrl }}"
           class="relative bg-white rounded-xl border px-4 py-3 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition
                  {{ ! $filter ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200' }}">
            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center"><x-icon name="pill" class="h-4 w-4"/></div>
            <div class="min-w-0 flex-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Total medicines') }}</div>
                <div class="text-lg font-extrabold text-slate-900 leading-tight">{{ $counts['total'] }}</div>
            </div>
            @if(! $filter)
                <span class="text-emerald-500" style="position:absolute;top:8px;right:8px;line-height:0;z-index:10;">{!! $activeCheck !!}</span>
            @endif
        </a>
        <a href="{{ $lowUrl }}"
           class="relative bg-white rounded-xl border px-4 py-3 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition
                  {{ $filter === 'low' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200' }}">
            <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><x-icon name="alert" class="h-4 w-4"/></div>
            <div class="min-w-0 flex-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Low stock') }}</div>
                <div class="text-lg font-extrabold text-amber-700 leading-tight">{{ $counts['low'] }}</div>
            </div>
            @if($filter === 'low')
                <span class="text-emerald-500" style="position:absolute;top:8px;right:8px;line-height:0;z-index:10;">{!! $activeCheck !!}</span>
            @endif
        </a>
        <a href="{{ $critUrl }}"
           class="relative bg-white rounded-xl border px-4 py-3 flex items-center gap-3 hover:shadow-md hover:-translate-y-0.5 transition
                  {{ $filter === 'critical' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200' }}">
            <div class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><x-icon name="alert" class="h-4 w-4"/></div>
            <div class="min-w-0 flex-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Critical') }}</div>
                <div class="text-lg font-extrabold text-rose-700 leading-tight">{{ $counts['critical'] }}</div>
            </div>
            @if($filter === 'critical')
                <span class="text-emerald-500" style="position:absolute;top:8px;right:8px;line-height:0;z-index:10;">{!! $activeCheck !!}</span>
            @endif
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-5 py-3">{{ __('Medicine') }}</th>
                        <th class="px-5 py-3">{{ __('Category') }}</th>
                        <th class="px-5 py-3">{{ __('Manufacturer') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Price') }}</th>
                        <th class="px-5 py-3">{{ __('Stock') }}</th>
                        <th class="px-5 py-3">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Update') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($medicines as $m)
                        @php
                            $stockClass = $m->stock > 50 ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                : ($m->stock > 10 ? 'bg-amber-50 text-amber-700 border-amber-100'
                                : 'bg-rose-50 text-rose-700 border-rose-100');
                            $dotClass = $m->stock > 50 ? 'bg-emerald-500'
                                : ($m->stock > 10 ? 'bg-amber-500'
                                : 'bg-rose-500');

                            if ($m->stock < 10) {
                                $statusLabel = __('Critical');
                                $statusBadge = 'bg-rose-100 text-rose-700 border-rose-200';
                                $statusDot = 'bg-rose-500 animate-pulse';
                            } elseif ($m->stock < 50) {
                                $statusLabel = __('Low');
                                $statusBadge = 'bg-amber-100 text-amber-700 border-amber-200';
                                $statusDot = 'bg-amber-500';
                            } else {
                                $statusLabel = __('Healthy');
                                $statusBadge = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                $statusDot = 'bg-emerald-500';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900">{{ $m->name }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest">{{ $m->unit }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-[11px]">{{ $m->category }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $m->manufacturer }}</td>
                            <td class="px-5 py-3 text-right font-bold text-slate-900">₹{{ number_format($m->price, 2) }}</td>
                            <td class="px-5 py-3">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-bold text-xs border {{ $stockClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }} {{ $m->stock <= 10 ? 'animate-pulse' : '' }}"></span>
                                    {{ $m->stock }}
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusBadge }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('pharmacist.inventory.update-stock', $m->id) }}" class="flex items-center justify-end gap-1.5">
                                    @csrf @method('PATCH')
                                    <input type="number" name="stock" value="{{ $m->stock }}" min="0"
                                           class="w-20 h-9 px-2.5 rounded-lg border border-slate-200 text-sm font-bold text-right focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition outline-none">
                                    <button class="h-9 w-9 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition flex items-center justify-center shadow-sm shadow-emerald-600/20" title="{{ __('Save') }}">
                                        <x-icon name="check" class="h-4 w-4"/>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center text-slate-500 font-medium">{{ __('No medicines found in your inventory.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($medicines->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50">{{ $medicines->links() }}</div>
        @endif
    </div>
</div>
@endsection
