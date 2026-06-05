@extends('layouts.public')
@section('title', __('About'))
@section('content')

{{-- ── About Us ── --}}
<section class="py-20 bg-white overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

        {{-- Photo (left) — framed portrait --}}
        <div class="relative flex justify-center lg:justify-start">
            {{-- decorative shapes --}}
            <div class="absolute -top-6 -left-6 h-28 w-28 rounded-2xl bg-teal-100 -z-10 hidden sm:block"></div>
            <div class="absolute -bottom-6 -right-2 h-32 w-32 rounded-full bg-emerald-100 -z-10 hidden sm:block"></div>

            <div class="relative w-full max-w-sm">
                <div class="rounded-3xl overflow-hidden shadow-2xl ring-1 ring-slate-200 bg-slate-100 aspect-[3/4]">
                    <img src="{{ asset('assets/site_images/founder.jpeg') }}"
                         alt="{{ __('Lalje Kumar — Founder, LK Healthcare') }}"
                         class="h-full w-full object-cover object-center"
                         onerror="this.onerror=null;this.src='{{ asset('assets/site_images/lklogo.png') }}';this.classList.add('p-16','object-contain');">
                </div>
                {{-- name badge --}}
                <div class="absolute -bottom-5 left-1/2 -translate-x-1/2 bg-white rounded-2xl shadow-lg px-6 py-3 text-center w-max max-w-[90%] ring-1 ring-slate-100">
                    <div class="font-extrabold text-slate-900 leading-tight">{{ __('Laljee Kumar') }}</div>
                    <div class="text-[11px] font-bold text-teal-600 uppercase tracking-widest mt-0.5">{{ __('Founder & CEO') }}</div>
                </div>
            </div>
        </div>

        {{-- Text (right) --}}
        <div>
            <span class="text-xs font-bold tracking-[0.25em] uppercase text-teal-600">{{ __('Who we are') }}</span>
            <h2 class="mt-3 text-4xl font-extrabold text-slate-900 tracking-tight">{{ __('About Us') }}</h2>
            <div class="mt-4 h-1 w-16 rounded-full bg-teal-500"></div>

            <p class="mt-6 text-lg font-semibold text-slate-800 leading-relaxed">{{ __('LK Healthcare was founded by Laljee Kumar to make quality care simple for every family.') }}</p>

            <p class="mt-4 text-slate-500 leading-relaxed">{{ __('Since the beginning, we have focused on bringing doctors, labs, pharmacies, and emergency services together on one trusted platform — so getting the right care never feels complicated.') }}</p>

            <p class="mt-4 text-slate-500 leading-relaxed">{{ __('All in all, LK Healthcare provides a complete range of services — consultations, lab tests, medicines, digital records, blood bank, and 24/7 emergency support — for families across India.') }}</p>

            {{-- quick highlights --}}
            <div class="mt-7 grid sm:grid-cols-2 gap-3">
                @foreach([__('Verified doctors'), __('Digital health records'), __('24/7 emergency support'), __('Secure & private')] as $point)
                    <div class="flex items-center gap-2.5 text-slate-700 font-medium">
                        <span class="h-6 w-6 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center shrink-0"><x-icon name="check" class="h-3.5 w-3.5"/></span>
                        {{ $point }}
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('public.services') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-teal-600 text-white font-bold hover:bg-teal-700 shadow-lg shadow-teal-200 transition">
                    {{ __('Read More') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ── Team ── --}}
<section class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="text-xs font-bold tracking-[0.25em] uppercase text-teal-600">{{ __('Our Team') }}</span>
        <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('The people behind the care') }}</h2>
        <p class="mt-4 text-slate-500 max-w-2xl mx-auto">{{ __('A passionate group of doctors, engineers, and problem-solvers working every day to make your health journey simpler.') }}</p>

        <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
            @foreach([
                ['n'=>'Dr. Ananya Sharma', 'r'=>__('Chief Medical Officer'), 'g'=>'from-teal-500 to-emerald-500', 'b'=>__('Leads our medical board and keeps clinical quality at the heart of everything.')],
                ['n'=>'Rohan Mehta', 'r'=>__('Head of Technology'), 'g'=>'from-sky-500 to-indigo-500', 'b'=>__('Builds the secure, fast platform that powers your entire health journey.')],
                ['n'=>'Priya Nair', 'r'=>__('Head of Operations'), 'g'=>'from-rose-500 to-orange-400', 'b'=>__('Makes sure every booking, report, and delivery runs smoothly, every time.')],
            ] as $m)
                <div class="group relative bg-white rounded-2xl border border-slate-200 p-7 hover:shadow-xl hover:-translate-y-1 hover:border-teal-200 transition">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-gradient-to-br {{ $m['g'] }} text-white flex items-center justify-center text-xl font-extrabold shadow-md ring-4 ring-white shrink-0">
                            {{ \Illuminate\Support\Str::of($m['n'])->explode(' ')->map(fn($w)=>$w[0] ?? '')->take(2)->implode('') }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $m['n'] }}</h3>
                            <span class="mt-1 inline-block text-[11px] font-bold uppercase tracking-widest text-teal-600 bg-teal-50 px-2.5 py-0.5 rounded-full">{{ $m['r'] }}</span>
                        </div>
                    </div>

                    <p class="mt-5 text-sm text-slate-500 leading-relaxed">{{ $m['b'] }}</p>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                        <a href="#" aria-label="LinkedIn" class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 hover:bg-teal-600 hover:text-white flex items-center justify-center transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.05-1.86-3.05-1.86 0-2.14 1.45-2.14 2.95v5.67H9.35V9h3.41v1.56h.05c.47-.9 1.63-1.86 3.36-1.86 3.59 0 4.25 2.36 4.25 5.43v6.32ZM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0Z"/></svg>
                        </a>
                        <a href="#" aria-label="Twitter" class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 hover:bg-teal-600 hover:text-white flex items-center justify-center transition">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.665l-5.215-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25h6.832l4.713 6.231Z"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Closing ── --}}
<section class="py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Let us take care of your health') }}</h2>
        <p class="mt-4 text-slate-500">{{ __('Join thousands of families who trust LK Healthcare every day.') }}</p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="px-8 py-3 rounded-lg bg-teal-600 text-white font-bold hover:bg-teal-700 shadow-lg shadow-teal-200 transition">{{ __('Get Started') }}</a>
            <a href="{{ route('public.doctors') }}" class="px-8 py-3 rounded-lg border-2 border-slate-200 text-slate-700 font-bold hover:border-teal-300 hover:text-teal-600 transition">{{ __('Find a Doctor') }}</a>
        </div>
    </div>
</section>
@endsection
