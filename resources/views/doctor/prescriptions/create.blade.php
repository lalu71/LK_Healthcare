@extends('layouts.app')
@section('title', __('Write Prescription'))
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="rxForm()">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Write Prescription') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Fill diagnosis and add medicines.') }}</p>
        </div>
    </div>

    @if(!$appointment)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-slate-600">{{ __('Open an appointment to write a prescription.') }}</div>
    @else
        <form method="POST" action="{{ route('doctor.prescriptions.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

            <div class="rounded-xl bg-teal-50 border border-teal-100 p-4 flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-white text-teal-600 flex items-center justify-center"><x-icon name="user" class="h-5 w-5"/></div>
                <div>
                    <div class="font-bold text-slate-900">{{ $appointment->patient->user->name }}</div>
                    <div class="text-xs text-slate-500">{{ $appointment->appointment_date->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Diagnosis') }}</label>
                <textarea name="diagnosis" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-violet-500 focus:border-violet-500"></textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-slate-700">{{ __('Medicines') }}</label>
                    <button type="button" @click="add()" class="text-xs font-semibold text-violet-600 hover:underline inline-flex items-center gap-1"><x-icon name="plus" class="h-4 w-4"/> {{ __('Add medicine') }}</button>
                </div>
                <div class="space-y-3">
                    <datalist id="medicine-list">
                        @foreach($medicines as $m)
                            <option value="{{ $m->name }}" data-id="{{ $m->id }}">{{ $m->category }} · ₹{{ $m->price }}</option>
                        @endforeach
                    </datalist>
                    <template x-for="(row, idx) in items" :key="idx">
                        <div class="grid sm:grid-cols-12 gap-2 items-center">
                            <div class="sm:col-span-3">
                                <input :name="'items['+idx+'][medicine_name]'" x-model="row.medicine_name" 
                                       @input="updateId(idx)"
                                       list="medicine-list" placeholder="{{ __('Medicine name') }}" 
                                       class="w-full rounded-lg border-slate-300 text-sm" required>
                                <input type="hidden" :name="'items['+idx+'][medicine_id]'" x-model="row.medicine_id">
                            </div>
                            <input :name="'items['+idx+'][dosage]'" x-model="row.dosage" placeholder="{{ __('Dosage') }}" class="sm:col-span-2 rounded-lg border-slate-300 text-sm" required>
                            <input :name="'items['+idx+'][frequency]'" x-model="row.frequency" placeholder="1-0-1" class="sm:col-span-2 rounded-lg border-slate-300 text-sm" required>
                            <input :name="'items['+idx+'][duration]'" x-model="row.duration" placeholder="5 days" class="sm:col-span-2 rounded-lg border-slate-300 text-sm" required>
                            <input :name="'items['+idx+'][instructions]'" x-model="row.instructions" placeholder="{{ __('Note') }}" class="sm:col-span-2 rounded-lg border-slate-300 text-sm">
                            <button type="button" @click="remove(idx)" class="sm:col-span-1 text-rose-500 hover:bg-rose-50 p-2 rounded-lg"><x-icon name="trash" class="h-4 w-4"/></button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-semibold text-slate-700">{{ __('Advice') }}</label>
                    <textarea name="advice" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-violet-500 focus:border-violet-500" placeholder="{{ __('Lifestyle, diet, follow-up notes') }}"></textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">{{ __('Follow-up date') }}</label>
                    <input type="date" name="follow_up_date" min="{{ now()->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-violet-500 focus:border-violet-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700"><x-icon name="check" class="h-4 w-4"/> {{ __('Save Prescription') }}</button>
            </div>
        </form>
    @endif
</div>

<script>
function rxForm(){
    const medList = @json($medicines->pluck('id', 'name'));
    return {
        items: [{medicine_id:'', medicine_name:'', dosage:'', frequency:'', duration:'', instructions:''}],
        add(){ this.items.push({medicine_id:'', medicine_name:'', dosage:'', frequency:'', duration:'', instructions:''}); },
        remove(i){ if(this.items.length>1) this.items.splice(i,1); },
        updateId(i){
            const name = this.items[i].medicine_name;
            this.items[i].medicine_id = medList[name] || '';
        }
    }
}
</script>
@endsection
