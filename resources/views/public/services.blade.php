@extends('layouts.public')
@section('title', __('Services'))
@section('content')
<section class="bg-slate-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-xs font-bold tracking-wider uppercase text-teal-600">{{ __('What we offer') }}</span>
        <h1 class="mt-3 text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight max-w-3xl leading-tight">{{ __('Healthcare services designed around you') }}</h1>
    </div>
</section>

@php
    // Color rotation — literal classes so Tailwind's purge keeps them.
    $serviceColors = ['teal', 'violet', 'emerald', 'rose', 'amber', 'sky', 'indigo', 'pink'];
@endphp
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($services->count() > 0)
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $i => $s)
                    @php $color = $serviceColors[$i % count($serviceColors)]; @endphp
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white hover:shadow-lg hover:border-teal-200 hover:-translate-y-1 transition">
                        <div class="h-12 w-12 rounded-xl bg-{{ $color }}-50 flex items-center justify-center overflow-hidden">
                            @if($s->image)
                                <img src="{{ asset('assets/service/' . $s->image) }}" alt="{{ $s->title }}" class="h-full w-full object-cover">
                            @else
                                <x-icon name="sparkles" class="h-6 w-6 text-{{ $color }}-600"/>
                            @endif
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $s->title }}</h3>
                        <p class="mt-2 text-slate-600 leading-relaxed text-sm">{{ $s->short_discription }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="mx-auto h-14 w-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <x-icon name="sparkles" class="h-7 w-7"/>
                </div>
                <p class="mt-4 text-slate-500 font-medium">{{ __('No services available right now.') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
