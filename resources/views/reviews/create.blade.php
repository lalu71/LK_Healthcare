@extends('layouts.app')
@section('title', __('Rate & Review'))
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Heading --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
            <x-icon name="chat" class="h-6 w-6"/>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Rate & Review') }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Tell us about your experience with LK Healthcare.') }}</p>
        </div>
    </div>

    {{-- Current review status --}}
    @if($review)
        <div class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ __('Your current review') }}</div>
                <div class="mt-1 text-amber-400 text-lg">{!! str_repeat('★', $review->rating) !!}<span class="text-slate-300">{!! str_repeat('★', 5 - $review->rating) !!}</span></div>
            </div>
            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $review->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                {{ $review->is_approved ? __('Approved — live on site') : __('Pending approval') }}
            </span>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('reviews.store') }}"
          x-data="{ rating: {{ (int) old('rating', $review->rating ?? 5) }} }"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5">
        @csrf

        <div>
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Your rating') }}</label>
            <div class="flex items-center gap-1 mt-2">
                <template x-for="s in 5" :key="s">
                    <button type="button" @click="rating = s"
                            class="text-4xl leading-none transition hover:scale-110"
                            :class="s <= rating ? 'text-amber-400' : 'text-slate-300'">★</button>
                </template>
                <span class="ml-3 text-sm font-semibold text-slate-600" x-text="rating + ' / 5'"></span>
                <input type="hidden" name="rating" :value="rating">
            </div>
            @error('rating')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Your review') }}</label>
            <textarea name="remark" required rows="5" maxlength="1000"
                      placeholder="{{ __('What did you like? What can we improve?') }}"
                      class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500">{{ old('remark', $review->remark ?? '') }}</textarea>
            @error('remark')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-400">{{ __('Reviews are shown publicly after admin approval.') }}</p>
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700 transition shrink-0">
                <x-icon name="check" class="h-4 w-4"/> {{ $review ? __('Update Review') : __('Submit Review') }}
            </button>
        </div>
    </form>
</div>
@endsection
