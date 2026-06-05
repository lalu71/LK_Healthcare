@extends('layouts.app')
@section('title', __('Blood Bank'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><x-icon name="droplet" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Blood Bank') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Live availability, donor registry, emergency requests.') }}</p>
        </div>
    </div>

    <h3 class="text-lg font-bold mb-3">{{ __('Current Availability') }}</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3 mb-10">
        @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
            @php $units = optional($inventory->firstWhere('blood_group',$bg))->units ?? 0; @endphp
            <div class="rounded-2xl p-4 text-center border border-slate-200 bg-white">
                <div class="text-3xl font-extrabold text-rose-600">{{ $bg }}</div>
                <div class="mt-1 text-xs {{ $units > 5 ? 'text-emerald-600' : ($units > 0 ? 'text-amber-600' : 'text-rose-600') }} font-semibold">{{ $units }} {{ __('units') }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <form method="POST" action="{{ route('blood.request') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            @csrf
            <h3 class="font-bold text-slate-900">{{ __('Request Blood') }}</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                <input name="patient_name" required placeholder="{{ __('Patient name') }}" class="rounded-lg border-slate-300">
                <select name="blood_group" required class="rounded-lg border-slate-300">
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option>{{ $bg }}</option>@endforeach
                </select>
                <input type="number" name="units" min="1" max="10" required placeholder="{{ __('Units') }}" class="rounded-lg border-slate-300">
                <input type="date" name="needed_by" min="{{ now()->format('Y-m-d') }}" class="rounded-lg border-slate-300">
                <input name="hospital" placeholder="{{ __('Hospital') }}" class="sm:col-span-2 rounded-lg border-slate-300">
                <input name="contact_phone" required placeholder="{{ __('Contact phone') }}" class="sm:col-span-2 rounded-lg border-slate-300">
                <textarea name="reason" placeholder="{{ __('Reason (optional)') }}" class="sm:col-span-2 rounded-lg border-slate-300" rows="2"></textarea>
            </div>
            <button class="w-full px-4 py-2.5 rounded-lg bg-rose-600 text-white font-semibold hover:bg-rose-700">{{ __('Submit Request') }}</button>
        </form>

        <form method="POST" action="{{ route('blood.donor') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            @csrf
            <h3 class="font-bold text-slate-900">{{ __('Register as Donor') }}</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                <input name="name" required placeholder="{{ __('Full name') }}" value="{{ auth()->user()->name }}" class="rounded-lg border-slate-300">
                <select name="blood_group" required class="rounded-lg border-slate-300">
                    <option value="">{{ __('Blood group') }}</option>
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option>{{ $bg }}</option>@endforeach
                </select>
                <input name="phone" required placeholder="{{ __('Phone') }}" value="{{ auth()->user()->phone }}" class="rounded-lg border-slate-300">
                <input name="city" placeholder="{{ __('City') }}" class="rounded-lg border-slate-300">
                <input type="date" name="last_donated_at" max="{{ now()->format('Y-m-d') }}" class="sm:col-span-2 rounded-lg border-slate-300" placeholder="{{ __('Last donated') }}">
            </div>
            <button class="w-full px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">{{ __('Register') }}</button>
        </form>
    </div>

    @if($myRequests->isNotEmpty())
        <div class="mt-10">
            <h3 class="text-lg font-bold mb-3">{{ __('My Requests') }}</h3>
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                        <tr><th class="px-5 py-2.5 text-left">Patient</th><th class="px-5 py-2.5 text-left">Group</th><th class="px-5 py-2.5 text-left">Units</th><th class="px-5 py-2.5 text-left">Status</th><th class="px-5 py-2.5 text-left">Date</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($myRequests as $r)
                            <tr>
                                <td class="px-5 py-3">{{ $r->patient_name }}</td>
                                <td class="px-5 py-3 font-bold text-rose-600">{{ $r->blood_group }}</td>
                                <td class="px-5 py-3">{{ $r->units }}</td>
                                <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-teal-100 text-teal-700">{{ $r->status }}</span></td>
                                <td class="px-5 py-3 text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
