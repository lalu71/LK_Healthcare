@extends('layouts.app')
@section('title', __('Book Appointment'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="bookWizard({ initialDoctorId: {{ $selectedDoctor?->id ?? 'null' }} })">

    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="calendar" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Book Appointment') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Select a doctor, pick a date, choose a slot.') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_380px] gap-6">
        <div>
            <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-4 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2 text-slate-500 flex-1 min-w-[180px]">
                    <x-icon name="search" class="h-5 w-5"/>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search doctor name') }}" class="flex-1 border-0 focus:ring-0 text-sm">
                </div>
                <select name="specialization" class="rounded-lg border-slate-300 text-sm">
                    <option value="">{{ __('All specialities') }}</option>
                    @foreach($specializations as $sp)
                        <option value="{{ $sp->id }}" @selected(request('specialization')==$sp->id)>{{ $sp->name }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">{{ __('Filter') }}</button>
            </form>

            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($doctors as $d)
                    <label class="block bg-white rounded-2xl border-2 transition cursor-pointer"
                           :class="doctorId == {{ $d->id }} ? 'border-teal-500 ring-2 ring-teal-100' : 'border-slate-200 hover:border-teal-300'"
                           @click="selectDoctor({{ $d->id }}, {{ (int)$d->consultation_fee }}, '{{ addslashes($d->user->name) }}')">
                        <div class="p-5 flex gap-4">
                            <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white font-extrabold text-xl flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($d->user->name,0,1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-900">Dr. {{ $d->user->name }}</div>
                                <div class="text-sm text-teal-600 font-semibold">{{ $d->specialization->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $d->experience_years }} {{ __('yrs experience') }}</div>
                                @if($d->availabilities->count() === 0)
                                    <div class="mt-2 text-[10px] inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-semibold">{{ __('No slots configured') }}</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-500">{{ __('Fee') }}</div>
                                <div class="font-extrabold text-slate-900">₹{{ number_format($d->consultation_fee,0) }}</div>
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="col-span-2 text-center py-12 bg-white rounded-2xl border border-slate-200">
                        <x-icon name="stethoscope" class="h-12 w-12 mx-auto text-slate-300"/>
                        <p class="mt-3 text-slate-500">{{ __('No doctors match your filters.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:sticky lg:top-20 self-start">
            <form method="POST" action="{{ route('patient.book.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
                @csrf
                <h3 class="text-lg font-bold text-slate-900">{{ __('Your Booking') }}</h3>

                <template x-if="!doctorId">
                    <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 text-slate-500 text-sm">
                        {{ __('Select a doctor to continue.') }}
                    </div>
                </template>

                <template x-if="doctorId">
                    <div class="space-y-4">
                        <div class="rounded-lg bg-teal-50 border border-teal-100 px-4 py-3">
                            <div class="text-xs text-teal-600">{{ __('Selected doctor') }}</div>
                            <div class="font-bold text-teal-900" x-text="'Dr. '+doctorName"></div>
                            <div class="text-sm text-teal-700">₹<span x-text="fee"></span></div>
                        </div>

                        <input type="hidden" name="doctor_id" :value="doctorId">

                        <div>
                            <label class="text-sm font-semibold text-slate-700">{{ __('Date') }}</label>
                            <input type="date" name="appointment_date" x-model="date" min="{{ now()->format('Y-m-d') }}" @change="loadSlots" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-700">{{ __('Time slot') }}</label>
                            <template x-if="loadingSlots">
                                <div class="mt-2 text-sm text-slate-500">{{ __('Loading slots…') }}</div>
                            </template>
                            <template x-if="!loadingSlots && date && slots.length===0">
                                <div class="mt-2 text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-lg p-3">{{ __('No slots on this day. Try another date.') }}</div>
                            </template>
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                <template x-for="s in slots" :key="s.time">
                                    <label :class="[s.taken ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer', slot===s.time ? 'bg-teal-600 text-white border-teal-600' : 'bg-slate-50 text-slate-700 border-slate-200 hover:border-teal-400']"
                                           class="text-xs font-semibold text-center py-2 rounded-lg border">
                                        <input type="radio" name="slot" class="hidden" :value="s.time" :disabled="s.taken" x-model="slot">
                                        <span x-text="s.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-700">{{ __('Reason for visit (optional)') }}</label>
                            <textarea name="reason" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500" placeholder="{{ __('e.g. fever, consultation…') }}"></textarea>
                        </div>

                        <button type="submit" :disabled="!slot" :class="!slot ? 'bg-slate-300 cursor-not-allowed' : 'bg-teal-600 hover:bg-teal-700'" class="w-full px-4 py-3 rounded-lg text-white font-semibold">
                            {{ __('Continue to Payment') }}
                        </button>
                        <p class="text-[11px] text-slate-400 text-center">{{ __('You will be asked to pay the consultation fee next.') }}</p>
                    </div>
                </template>
            </form>
        </div>
    </div>
</div>

<script>
function bookWizard(init){
    return {
        doctorId: init.initialDoctorId,
        doctorName: @json($selectedDoctor?->user->name ?? ''),
        fee: @json((int)($selectedDoctor?->consultation_fee ?? 0)),
        date: '',
        slot: '',
        slots: [],
        loadingSlots: false,
        selectDoctor(id, fee, name){
            this.doctorId = id; this.fee = fee; this.doctorName = name;
            this.slot = ''; this.slots = [];
            if(this.date) this.loadSlots();
        },
        async loadSlots(){
            if(!this.doctorId || !this.date) return;
            this.loadingSlots = true; this.slots = []; this.slot='';
            const url = '{{ url("/patient/doctors") }}/'+this.doctorId+'/slots?date='+this.date;
            try {
                const res = await fetch(url, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
                const data = await res.json();
                this.slots = data.slots || [];
            } catch(e) { console.error(e); }
            this.loadingSlots = false;
        }
    }
}
</script>
@endsection
