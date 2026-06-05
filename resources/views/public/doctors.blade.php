@extends('layouts.public')
@section('title', __('Our Doctors'))
@section('content')
<section class="bg-slate-100 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">{{ __('Our verified doctors') }}</h1>
        <p class="mt-3 text-slate-600 max-w-xl">{{ __('Find experienced specialists across every major speciality.') }}</p>
        <form method="GET" class="mt-8 grid sm:grid-cols-[1fr_auto] gap-3 max-w-2xl">
            <select name="specialization" class="rounded-xl border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-3">
                <option value="">{{ __('All specialities') }}</option>
                @foreach($specializations as $sp)
                    <option value="{{ $sp->id }}" @selected(request('specialization')==$sp->id)>{{ $sp->name }}</option>
                @endforeach
            </select>
            <button class="px-6 py-3 rounded-xl bg-teal-600 text-white font-semibold hover:bg-teal-700">{{ __('Filter') }}</button>
        </form>
    </div>
</section>

<section class="py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($doctors->isEmpty())
            <div class="text-center py-12 text-slate-500">{{ __('No doctors found. Please try another speciality.') }}</div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($doctors as $d)
                    <div class="bg-white rounded-2xl border border-slate-200 hover:shadow-xl hover:border-teal-200 transition p-6 text-center">
                        <div class="h-24 w-24 mx-auto rounded-2xl overflow-hidden shadow-md ring-4 ring-teal-50">
                            @if(str_contains(strtolower($d->user->name), 'priyanka'))
                                <img src="{{ asset('assets/doctors/priyanka.png') }}" alt="Dr. {{ $d->user->name }}" class="h-full w-full object-cover">
                            @else
                                <img src="https://i.pravatar.cc/150?u={{ $d->user->email }}" alt="Dr. {{ $d->user->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <h3 class="mt-4 font-bold text-slate-900 text-lg">Dr. {{ $d->user->name }}</h3>
                        <p class="text-teal-600 text-sm font-semibold">{{ __($d->specialization->name ?? 'General') }}</p>
                        <div class="mt-3 flex items-center justify-center gap-1 text-xs text-slate-500">
                            <x-icon name="clock" class="h-3.5 w-3.5"/> {{ $d->experience_years }} {{ __('years experience') }}
                        </div>
                        @if($d->qualification)
                            <div class="mt-1 text-xs text-slate-500">{{ $d->qualification }}</div>
                        @endif
                        <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
                            <span class="font-bold text-slate-900">₹{{ number_format($d->consultation_fee,0) }}</span>
                            @auth
                                <a href="{{ route('patient.book', ['doctor_id'=>$d->id]) }}" class="px-4 py-2 rounded-lg bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">{{ __('Book') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">{{ __('Book') }}</a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
