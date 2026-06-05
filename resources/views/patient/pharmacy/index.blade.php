@extends('layouts.app')
@section('title', __('Pharmacy'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="cart()">

    <div class="flex items-center gap-3 mb-6">
        <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Pharmacy') }}</h1>
            <p class="text-slate-500 text-sm">{{ __('Order medicines · delivery in 4 hours') }}</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl border border-slate-200 p-4 mb-4 flex items-center gap-3">
        <div class="flex items-center gap-2 text-slate-500 flex-1">
            <x-icon name="search" class="h-5 w-5"/>
            <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('Search medicine') }}" class="flex-1 border-0 focus:ring-0 text-sm">
        </div>
        <button class="px-4 py-2 rounded-lg bg-teal-600 text-white text-sm font-semibold hover:bg-teal-700">{{ __('Search') }}</button>
    </form>

    <div class="grid lg:grid-cols-[1fr_340px] gap-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($medicines as $m)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition flex flex-col">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><x-icon name="pill" class="h-5 w-5"/></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 truncate">{{ __($m->name) }}</div>
                            <div class="text-xs text-slate-500">{{ __($m->manufacturer ?? $m->category) }}</div>
                            <div class="mt-1 text-[10px] inline-flex items-center px-1.5 py-0.5 rounded bg-slate-50 text-slate-600 font-semibold uppercase">{{ __($m->unit) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <div class="text-lg font-extrabold text-slate-900">₹{{ number_format($m->price,2) }}</div>
                            <div class="text-[11px] {{ $m->stock > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $m->stock > 0 ? __('In stock') . ' · '.$m->stock : __('Out of stock') }}</div>
                        </div>
                        <button type="button" @click="add({{ $m->id }}, '{{ addslashes($m->name) }}', {{ $m->price }})"
                                :disabled="{{ $m->stock > 0 ? 'false' : 'true' }}"
                                class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed">
                            <span class="inline-flex items-center gap-1"><x-icon name="plus" class="h-4 w-4"/> {{ __('Add') }}</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
                    <x-icon name="pill" class="h-12 w-12 mx-auto text-slate-300"/>
                    <p class="mt-3 text-slate-500">{{ __('No medicines found.') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Cart --}}
        <form method="POST" action="{{ route('patient.pharmacy.order') }}" class="bg-white rounded-2xl border border-slate-200 p-5 lg:sticky lg:top-20 self-start">
            @csrf
            <h3 class="font-bold text-slate-900">{{ __('Your Cart') }}</h3>

            <template x-if="items.length===0">
                <p class="text-slate-500 text-sm mt-3">{{ __('Cart is empty. Add medicines.') }}</p>
            </template>

            <div class="mt-3 space-y-3">
                <template x-for="(it, i) in items" :key="it.id">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold truncate" x-text="it.name"></div>
                            <div class="text-xs text-slate-500">₹<span x-text="it.price.toFixed(2)"></span> × <span x-text="it.qty"></span></div>
                        </div>
                        <input type="hidden" :name="'items['+i+'][medicine_id]'" :value="it.id">
                        <input type="number" min="1" max="20" :name="'items['+i+'][quantity]'" x-model.number="it.qty" class="w-16 rounded-lg border-slate-300 text-xs py-1">
                        <button type="button" @click="remove(i)" class="text-rose-500 hover:bg-rose-50 p-1 rounded"><x-icon name="trash" class="h-4 w-4"/></button>
                    </div>
                </template>
            </div>

            <template x-if="items.length > 0">
                <div class="mt-4 pt-4 border-t border-slate-200 space-y-3 text-sm">
                    <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>₹<span x-text="subtotal().toFixed(2)"></span></span></div>
                    <div class="flex justify-between"><span>{{ __('Delivery') }}</span><span x-text="subtotal()<499 ? '₹40' : 'FREE'"></span></div>
                    <div class="flex justify-between font-extrabold text-base"><span>{{ __('Total') }}</span><span>₹<span x-text="total().toFixed(2)"></span></span></div>

                    <div class="space-y-2 pt-2">
                        <input name="delivery_address" required placeholder="{{ __('Delivery address') }}" class="w-full rounded-lg border-slate-300 text-sm">
                        <input name="delivery_phone" required placeholder="{{ __('Phone') }}" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>

                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                        {{ __('Place Order') }}
                    </button>
                </div>
            </template>
        </form>
    </div>

    @if($orders->isNotEmpty())
        <div class="mt-10">
            <h2 class="text-xl font-bold text-slate-900 mb-4">{{ __('Recent Orders') }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($orders as $o)
                    <div class="bg-white rounded-2xl border border-slate-200 p-5">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-mono text-slate-500">{{ $o->order_code }}</div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-teal-100 text-teal-700">{{ __($o->status) }}</span>
                        </div>
                        <div class="mt-2 text-sm text-slate-600">{{ $o->items->count() }} {{ __('items') }} · ₹{{ number_format($o->total,0) }}</div>
                        <div class="mt-2 text-xs text-slate-400">{{ $o->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
function cart(){
    return {
        items: [],
        add(id, name, price){
            let it = this.items.find(x=>x.id===id);
            if(it){ it.qty = Math.min(20, it.qty+1); }
            else { this.items.push({id, name, price: Number(price), qty:1}); }
        },
        remove(i){ this.items.splice(i,1); },
        subtotal(){ return this.items.reduce((s,it)=>s+it.price*it.qty,0); },
        total(){ return this.subtotal() + (this.subtotal()<499 ? 40 : 0); }
    }
}
</script>
@endsection
