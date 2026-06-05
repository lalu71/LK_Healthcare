@extends('layouts.public')
@section('title', __('Welcome'))
@section('content')

{{-- ░░░░░░░░░░░░░░░░░░░░ HERO ░░░░░░░░░░░░░░░░░░░░ --}}
<section class="relative overflow-hidden">
    <div class="relative min-h-screen lg:min-h-[100vh] overflow-hidden">

        {{-- ░░ Background layers ░░ --}}
        {{-- 1. Hospital photo --}}
        <img src="https://images.unsplash.com/photo-1538108149393-fbbd81895907?auto=format&fit=crop&w=1920&q=80"
             alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover"
             referrerpolicy="no-referrer">
        {{-- 2. Teal horizontal gradient with multiply so hospital is tinted --}}
        <div class="absolute inset-0 bg-gradient-to-r from-teal-600 via-teal-500 to-emerald-500" style="mix-blend-mode: multiply;"></div>
        {{-- 3. Soft brand wash for cohesion --}}
        <div class="absolute inset-0 bg-gradient-to-r from-teal-700/50 via-teal-500/30 to-emerald-500/40"></div>
        {{-- 4. Left-side darken so the headline stays crisp --}}
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/40 via-slate-900/10 to-transparent"></div>

        {{-- ░░ Soft glow halo behind doctor (depth, no harsh edges) ░░ --}}
        <div class="hidden lg:block absolute right-[5%] bottom-0 h-[85%] w-[40%] rounded-t-full bg-gradient-radial from-white/20 via-emerald-300/10 to-transparent z-[1] blur-2xl pointer-events-none"
             style="background: radial-gradient(ellipse at center, rgba(255,255,255,0.25), rgba(16,185,129,0.08) 50%, transparent 80%);"></div>

        {{-- ░░ Male doctor image — cutout style, NO background ░░ --}}
        <img src="{{ asset('assets/doctors/doctor3.png') }}?auto=format&fit=crop&w=900&q=90"
             alt="{{ __('Verified medical professional') }}"
             loading="eager"
             referrerpolicy="no-referrer"
             class="hidden lg:block absolute right-0 bottom-0 h-[95%] w-auto max-w-[50%] object-cover object-bottom z-[2]"
             >

        {{-- ░░ Content ░░ --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full grid lg:grid-cols-12 gap-8 items-center py-12 lg:py-0">

            {{-- LEFT: Headline + CTA --}}
            <div class="lg:col-span-7 text-white space-y-7">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 backdrop-blur text-white text-[11px] font-bold uppercase tracking-widest border border-white/20 mt-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                    {{ __('Trusted by') }} {{ number_format($usersCount) }}+ {{ __('families') }}
                </span>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.05] drop-shadow-lg">
                    {{ $siteContent->site_title ?? __('Take Care of Your Health') }}
                </h1>

                <p class="text-lg text-teal-50/95 max-w-md leading-relaxed">
                    {{ $siteContent->site_description ?? __('Book appointments with verified doctors, access lab reports, order medicines, and manage your health — all in one secure platform.') }}
                </p>

                <div class="flex flex-wrap items-center gap-5">
                    {{-- Get Started --}}
                    <a href="{{ route('register') }}"
                       class="group relative inline-flex items-center gap-3 rounded-full bg-white text-teal-700 font-bold text-sm leading-none shadow-lg shadow-teal-900/20 hover:shadow-2xl hover:shadow-teal-900/40 transition-all hover:-translate-y-0.5"
                       style="padding: 6px 6px 6px 28px;">
                        <span>{{ __('Get Started') }}</span>
                        <span class="h-10 w-10 rounded-full bg-teal-600 text-white flex items-center justify-center group-hover:bg-teal-700 group-hover:translate-x-0.5 transition shrink-0">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                    </a>

                    {{-- Explore Services --}}
                    <a href="{{ route('public.services') }}"
                       class="inline-flex items-center justify-center rounded-full border-2 border-white/50 text-white font-bold text-sm leading-none hover:bg-white hover:text-teal-700 hover:border-white transition-all"
                       style="padding: 18px 32px;">
                        {{ __('Explore Services') }}
                    </a>
                </div>

                {{-- Stats --}}
                <div class="pt-3 grid grid-cols-3 gap-4 max-w-md">
                    <div>
                        <div class="text-3xl font-extrabold">{{ $doctorsCount }}+</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-teal-50/80 mt-0.5">{{ __('Doctors') }}</div>
                    </div>
                    <div class="border-l border-white/30 pl-4">
                        <div class="text-3xl font-extrabold">{{ $specialitiesCount }}+</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-teal-50/80 mt-0.5">{{ __('Specialities') }}</div>
                    </div>
                    <div class="border-l border-white/30 pl-4">
                        <div class="text-3xl font-extrabold">24/7</div>
                        <div class="text-[10px] font-bold uppercase tracking-widest text-teal-50/80 mt-0.5">{{ __('Support') }}</div>
                    </div>
                </div>

                {{-- Social row --}}
                <div class="flex items-center gap-3 pt-2">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-teal-50/70">{{ __('Follow') }}</span>
                    <div class="flex items-center gap-2">
                        <a href="#" class="h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center text-white transition" aria-label="Facebook">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12Z"/></svg>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center text-white transition" aria-label="Twitter">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.665l-5.215-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25h6.832l4.713 6.231Z"/></svg>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center text-white transition" aria-label="Instagram">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.43.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.43-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.43-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16Zm0 1.95c-3.14 0-3.51.01-4.75.07-1.07.05-1.65.23-2.04.38-.51.2-.88.44-1.27.83-.39.39-.63.76-.83 1.27-.15.39-.33.97-.38 2.04-.06 1.24-.07 1.61-.07 4.75s.01 3.51.07 4.75c.05 1.07.23 1.65.38 2.04.2.51.44.88.83 1.27.39.39.76.63 1.27.83.39.15.97.33 2.04.38 1.24.06 1.61.07 4.75.07s3.51-.01 4.75-.07c1.07-.05 1.65-.23 2.04-.38.51-.2.88-.44 1.27-.83.39-.39.63-.76.83-1.27.15-.39.33-.97.38-2.04.06-1.24.07-1.61.07-4.75s-.01-3.51-.07-4.75c-.05-1.07-.23-1.65-.38-2.04a3.4 3.4 0 0 0-.83-1.27 3.4 3.4 0 0 0-1.27-.83c-.39-.15-.97-.33-2.04-.38-1.24-.06-1.61-.07-4.75-.07Zm0 3.32a4.57 4.57 0 1 1 0 9.14 4.57 4.57 0 0 1 0-9.14Zm0 7.54a2.97 2.97 0 1 0 0-5.94 2.97 2.97 0 0 0 0 5.94Zm5.81-7.74a1.07 1.07 0 1 1-2.14 0 1.07 1.07 0 0 1 2.14 0Z"/></svg>
                        </a>
                        <a href="#" class="h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center text-white transition" aria-label="LinkedIn">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.02-3.05-1.86-3.05-1.86 0-2.14 1.45-2.14 2.95v5.67H9.35V9h3.41v1.56h.05c.47-.9 1.63-1.86 3.36-1.86 3.59 0 4.25 2.36 4.25 5.43v6.32ZM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0Z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- RIGHT: empty column (doctor image is the absolute layer behind) --}}
            <div class="hidden lg:block lg:col-span-5"></div>
        </div>

        {{-- Bottom curve mask --}}
        <svg class="absolute bottom-0 left-0 w-full z-[5]" viewBox="0 0 1440 80" preserveAspectRatio="none" style="height:50px;">
            <path d="M0,80 C240,40 480,0 720,0 C960,0 1200,40 1440,80 L1440,80 L0,80 Z" fill="white"/>
        </svg>
    </div>
</section>

{{-- Services (dynamic from DB) --}}
@php
    $homeServices = \App\Models\Service::where('status', 1)->latest()->limit(8)->get();
    // Color rotation — literal classes for Tailwind purge. Add classes the picker can see.
    $serviceColors = ['teal', 'violet', 'emerald', 'rose', 'amber', 'sky', 'indigo', 'pink'];
@endphp
@if($homeServices->count() > 0)
<section class="py-16 bg-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">{{ __('Complete care, one platform') }}</h2>
            <p class="mt-4 text-slate-600">{{ __('From consultation to recovery — we cover every step of your health journey.') }}</p>
        </div>
        <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($homeServices as $i => $s)
                @php $color = $serviceColors[$i % count($serviceColors)]; @endphp
                <div class="group bg-white rounded-2xl p-6 border border-slate-200 hover:border-teal-200 hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="h-12 w-12 rounded-xl bg-{{ $color }}-50 flex items-center justify-center overflow-hidden">
                        @if($s->image)
                            <img src="{{ asset('assets/service/' . $s->image) }}" alt="{{ $s->title }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="sparkles" class="h-6 w-6 text-{{ $color }}-600"/>
                        @endif
                    </div>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $s->title }}</h3>
                    <p class="mt-1 text-sm text-slate-500 leading-relaxed">{{ $s->short_discription }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Specilists (dynamic from DB) --}}
@php
    $homeDoctors = \App\Models\Doctor::with('user', 'specialization')
        ->where('is_active', true)
        ->latest()
        ->limit(8)
        ->get();
@endphp
@if($homeDoctors->count() > 0)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Meet our doctors') }}</h2>
                <p class="mt-2 text-slate-600">{{ __('Verified specialists across every major speciality.') }}</p>
            </div>
            <a href="{{ route('public.doctors') }}" class="text-teal-600 font-semibold hover:underline">{{ __('See all doctors') }} →</a>
        </div>
        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($homeDoctors as $d)
                <div class="bg-white rounded-2xl border border-slate-200 hover:shadow-xl hover:border-teal-200 transition p-6 text-center">
                    <div class="h-24 w-24 mx-auto rounded-2xl overflow-hidden shadow-md ring-4 ring-teal-50">
                        @if($d->user->avatar)
                            <img src="{{ asset($d->user->avatar) }}" alt="Dr. {{ $d->user->name }}" class="h-full w-full object-cover">
                        @elseif(str_contains(strtolower($d->user->name), 'priyanka'))
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
                    <div class="mt-4 pt-4 border-t border-slate-200 flex items-center justify-between">
                        <span class="font-bold text-slate-900">₹{{ number_format($d->consultation_fee, 0) }}</span>
                        @auth
                            <a href="{{ route('patient.book', ['doctor_id'=>$d->id]) }}" class="px-4 py-2 rounded-lg bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">{{ __('Book') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700">{{ __('Book') }}</a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- Testimonials --}}
<section class="py-16 bg-slate-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Loved by patients') }}</h2>
            <p class="mt-3 text-slate-600">{{ __('Real stories from real people.') }}</p>
        </div>
    </div>

    @php
        // Approved reviews from DB; fall back to defaults so the marquee never looks empty.
        $cards = $reviews->map(fn($r) => [
            'n' => $r->user->name ?? __('User'),
            'c' => __('Verified user'),
            't' => $r->remark,
            'r' => (int) $r->rating,
            'img' => $r->user && $r->user->avatar ? asset($r->user->avatar) : null,
        ])->toArray();

        if (empty($cards)) {
            $cards = [
                ['n'=>'Rohan S.','c'=>'Delhi','t'=>'Booked a cardiologist in minutes. Got my ECG reports digitally the next morning.','r'=>5],
                ['n'=>'Priya M.','c'=>'Mumbai','t'=>'Pharmacy delivery under 3 hours saved my mom in a tough week. Superb service.','r'=>5],
                ['n'=>'Amit K.','c'=>'Bangalore','t'=>'The emergency SOS got an ambulance in 9 minutes. Literally a life-saver.','r'=>5],
                ['n'=>'Sneha R.','c'=>'Pune','t'=>'Lab booking and home sample collection was super smooth. Reports in 24 hours.','r'=>5],
                ['n'=>'Vikram J.','c'=>'Hyderabad','t'=>'Connected with the best dermatologist online. Saved me hours of travel and waiting.','r'=>5],
                ['n'=>'Anjali T.','c'=>'Kolkata','t'=>'The blood bank helped find a donor for my dad within hours. Truly thankful.','r'=>5],
            ];
        }
    @endphp

    @if(count($cards) >= 4)
        {{-- Marquee track. We duplicate the list so the loop is seamless when track shifts -50%. --}}
        <div class="relative mt-12 marquee-wrap">
            <div class="marquee-track flex gap-6 w-max">
                @foreach(array_merge($cards, $cards) as $i => $t)
                    <div class="lk-rx-card bg-white rounded-2xl p-6 border border-slate-200 shadow-sm shrink-0">
                        <div class="flex items-center gap-0.5 text-amber-400">{!! str_repeat('★', $t['r']) !!}<span class="text-slate-300">{!! str_repeat('★', 5 - $t['r']) !!}</span></div>
                        <p class="mt-4 text-slate-700 leading-relaxed">"{{ __($t['t']) }}"</p>
                        <div class="mt-5 flex items-center gap-3">
                            @if(!empty($t['img']))
                                <img src="{{ $t['img'] }}" alt="{{ $t['n'] }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold shrink-0">{{ substr($t['n'],0,1) }}</div>
                            @endif
                            <div>
                                <div class="font-bold text-slate-900">{{ __($t['n']) }}</div>
                                <div class="text-xs text-slate-500">{{ __($t['c']) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Edge fades for polished marquee look --}}
            <div class="pointer-events-none absolute inset-y-0 left-0 w-16 sm:w-24 bg-gradient-to-r from-slate-100 to-transparent"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-16 sm:w-24 bg-gradient-to-l from-slate-100 to-transparent"></div>
        </div>
    @else
        {{-- Few reviews: show each once, centered, no scrolling/duplication. --}}
        <div class="mt-12 flex flex-wrap justify-center gap-6">
            @foreach($cards as $t)
                <div class="lk-rx-card bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-0.5 text-amber-400">{!! str_repeat('★', $t['r']) !!}<span class="text-slate-300">{!! str_repeat('★', 5 - $t['r']) !!}</span></div>
                    <p class="mt-4 text-slate-700 leading-relaxed">"{{ __($t['t']) }}"</p>
                    <div class="mt-5 flex items-center gap-3">
                        @if(!empty($t['img']))
                            <img src="{{ $t['img'] }}" alt="{{ $t['n'] }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200 shrink-0">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold shrink-0">{{ substr($t['n'],0,1) }}</div>
                        @endif
                        <div>
                            <div class="font-bold text-slate-900">{{ __($t['n']) }}</div>
                            <div class="text-xs text-slate-500">{{ __($t['c']) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<style>
    @keyframes lkMarquee {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .marquee-track {
        animation: lkMarquee 10s linear infinite;
        animation-play-state: running !important;
        will-change: transform;
    }
    /* Bulletproof responsive card width — runs without Tailwind rebuild */
    .lk-rx-card { width: 78vw; max-width: 400px; }
    @media (min-width: 640px) { .lk-rx-card { width: 44vw; } }
    @media (min-width: 768px) { .lk-rx-card { width: 27vw; } }
</style>

@endsection
