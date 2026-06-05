@props([
    'action',
    'q' => '',
    'placeholder' => 'Search...',
    'hasFilters' => false,
])

{{-- Reusable list filter bar: rounded search pill + optional selects (slot) + Search button --}}
<form method="GET" action="{{ $action }}" class="flex items-center gap-3 flex-wrap">
    <div class="flex items-center bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11">
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $placeholder }}"
               class="h-10 border-0 p-0 focus:ring-0 text-sm font-medium text-slate-700 placeholder-slate-400 w-44 sm:w-60 bg-transparent">
    </div>

    {{ $slot }}

    <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Search') }}</button>

    @if($hasFilters)
        <a href="{{ $action }}"
           class="h-11 inline-flex items-center gap-1.5 px-3 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
            <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
        </a>
    @endif
</form>
