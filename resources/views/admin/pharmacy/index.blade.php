@extends('layouts.app')
@section('title', __('Pharmacy'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="pill" class="h-6 w-6"/></div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Pharmacy Inventory') }}</h1>
        </div>
        {{-- Two separate pill boxes: search + status --}}
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            {{-- Search pill --}}
            <div class="flex items-center gap-1.5 bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11 px-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Madicine Name') }}"
                       class="h-10 px-1 border-0 focus:ring-0 text-xs font-medium text-slate-700 w-52 bg-transparent">
            </div>

            {{-- Status pill --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-2 h-11 flex items-center">
                <select name="status" class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                    <option value="">{{ __('All status') }}</option>
                    <option value="critical" @selected(($status ?? '')==='critical')>{{ __('Critical') }}</option>
                    <option value="low" @selected(($status ?? '')==='low')>{{ __('Low') }}</option>
                    <option value="healthy" @selected(($status ?? '')==='healthy')>{{ __('Healthy') }}</option>
                </select>
            </div>

            <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Filter') }}</button>
            @if(!empty($q) || !empty($status))
                <a href="{{ route('admin.pharmacy.index') }}"
                   class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                    <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                </a>
            @endif
        </form>
    </div>

    <div class="grid lg:grid-cols-[380px_1fr] gap-6">
        <form method="POST" action="{{ route('admin.pharmacy.store') }}" class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3 h-fit">
            @csrf
            <h3 class="font-bold">{{ __('Add Medicine') }}</h3>
            <input name="name" required placeholder="{{ __('Name') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <div class="grid grid-cols-2 gap-2">
                <input name="category" placeholder="{{ __('Category') }}" class="rounded-lg border-slate-300 text-sm">
                <input name="manufacturer" placeholder="{{ __('Mfr.') }}" class="rounded-lg border-slate-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <input type="number" step="0.01" name="price" required placeholder="₹" class="rounded-lg border-slate-300 text-sm">
                <input type="number" name="stock" required placeholder="{{ __('Stock') }}" class="rounded-lg border-slate-300 text-sm">
            </div>
            <select name="unit" class="w-full rounded-lg border-slate-300 text-sm">
                <option value="strip">{{ __('Strip') }}</option>
                <option value="bottle">{{ __('Bottle') }}</option>
                <option value="tube">{{ __('Tube') }}</option>
                <option value="tablet">{{ __('Tablet') }}</option>
                <option value="box">{{ __('Box') }}</option>
            </select>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="requires_prescription" value="1" class="rounded"> {{ __('Requires prescription') }}</label>
            <button class="w-full px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700">{{ __('Add') }}</button>
        </form>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-3 text-left">{{ __('Name') }}</th><th class="p-3 text-left">{{ __('Mfr.') }}</th><th class="p-3 text-left">{{ __('Stock') }}</th><th class="p-3 text-left">{{ __('Status') }}</th><th class="p-3 text-left">{{ __('Price') }}</th><th class="p-3 text-right">{{ __('Action') }}</th></tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($medicines as $m)
                        @php
                            if ($m->stock < 10) {
                                $statusLabel = __('Critical');
                                $statusClass = 'bg-rose-100 text-rose-700 border-rose-200';
                                $dotClass = 'bg-rose-500 animate-pulse';
                            } elseif ($m->stock < 50) {
                                $statusLabel = __('Low');
                                $statusClass = 'bg-amber-100 text-amber-700 border-amber-200';
                                $dotClass = 'bg-amber-500';
                            } else {
                                $statusLabel = __('Healthy');
                                $statusClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                $dotClass = 'bg-emerald-500';
                            }
                        @endphp
                        <tr>
                            <td class="p-3 font-semibold">{{ $m->name }}</td>
                            <td class="p-3 text-slate-500">{{ $m->manufacturer ?? '-' }}</td>
                            <td class="p-3"><span class="{{ $m->stock > 10 ? 'text-emerald-600' : 'text-amber-600' }} font-bold">{{ $m->stock }}</span></td>
                            <td class="p-3">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="p-3">₹{{ number_format($m->price,2) }}</td>
                            <td class="p-3 text-right">
                                <form method="POST" action="{{ route('admin.pharmacy.destroy',$m->id) }}">@csrf @method('DELETE')
                                    <button class="text-xs text-rose-600 hover:underline">{{ __('Deactivate') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-50">{{ $medicines->links() }}</div>
        </div>
    </div>

    {{-- Pharmacist users management --}}
    <div class="grid lg:grid-cols-[380px_1fr] gap-6">
        {{-- Add Pharmacist form --}}
        <form method="POST" action="{{ route('admin.pharmacy.pharmacists.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3 h-fit">
            @csrf
            <div class="flex items-center gap-2 mb-2">
                <div class="h-9 w-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center"><x-icon name="user" class="h-5 w-5"/></div>
                <h3 class="font-bold">{{ __('Add Pharmacist') }}</h3>
            </div>
            <input name="name" required value="{{ old('name') }}" placeholder="{{ __('Full name') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <input type="email" name="email" required value="{{ old('email') }}" placeholder="{{ __('Email') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <input name="phone" value="{{ old('phone') }}" placeholder="{{ __('Phone (optional)') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <input type="password" name="password" required placeholder="{{ __('Password (min 6)') }}" minlength="6" class="w-full rounded-lg border-slate-300 text-sm">
            <div>
                <label class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">{{ __('Profile Picture') }}</label>
                <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="mt-1 w-full rounded-lg border-slate-300 text-xs py-1.5">
                @error('avatar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button class="w-full px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700">{{ __('Create Pharmacist') }}</button>
            <p class="text-[10px] font-semibold text-slate-400">{{ __('A login will be created for this pharmacist with the given email & password.') }}</p>
        </form>

        {{-- Pharmacists list --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">{{ __('Pharmacists') }}</h3>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-50 px-2.5 py-1 rounded-md">{{ $pharmacists->count() }} {{ __('total') }}</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                    <tr>
                        <th class="p-3 text-left">{{ __('Name') }}</th>
                        <th class="p-3 text-left">{{ __('Email') }}</th>
                        <th class="p-3 text-left">{{ __('Phone') }}</th>
                        <th class="p-3 text-left">{{ __('Status') }}</th>
                        <th class="p-3 text-right">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pharmacists as $p)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 font-semibold text-slate-900">{{ $p->name }}</td>
                            <td class="p-3 text-slate-600">{{ $p->email }}</td>
                            <td class="p-3 text-slate-500">{{ $p->phone ?: '—' }}</td>
                            <td class="p-3">
                                @if($p->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">{{ __('Active') }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-700">{{ __('Blocked') }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <form method="POST" action="{{ route('admin.pharmacy.pharmacists.toggle', $p->id) }}"
                                      onsubmit="return confirm('{{ $p->is_active ? __('Deactivate this pharmacist?') : __('Activate this pharmacist?') }}')">
                                    @csrf @method('PATCH')
                                    <button class="text-xs font-semibold hover:underline {{ $p->is_active ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $p->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-400 text-sm">{{ __('No pharmacists yet. Add one using the form on the left.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-icon name="cart" class="h-4 w-4"/>
                </div>
                <h3 class="font-bold text-slate-800">{{ __('Recent Orders') }}</h3>
            </div>
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-50 px-2.5 py-1 rounded-md">{{ $orders->count() }} {{ __('orders') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/60 text-[11px] uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-bold">{{ __('Code') }}</th>
                        <th class="px-4 py-3 text-left font-bold">{{ __('Patient') }}</th>
                        <th class="px-4 py-3 text-left font-bold">{{ __('Total') }}</th>
                        <th class="px-4 py-3 text-left font-bold">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left font-bold">{{ __('Date') }}</th>
                        <th class="px-5 py-3 text-right font-bold">{{ __('Update') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $orderStatusColors = [
                            'placed'    => ['bg-amber-100', 'text-amber-700',  'bg-amber-500'],
                            'packed'    => ['bg-sky-100',   'text-sky-700',    'bg-sky-500'],
                            'shipped'   => ['bg-violet-100','text-violet-700', 'bg-violet-500'],
                            'delivered' => ['bg-emerald-100','text-emerald-700','bg-emerald-500'],
                            'cancelled' => ['bg-rose-100',  'text-rose-700',   'bg-rose-500'],
                        ];
                    @endphp
                    @forelse($orders as $o)
                        @php $colors = $orderStatusColors[$o->status] ?? ['bg-slate-100','text-slate-700','bg-slate-500']; @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3 font-mono text-[11px] font-bold text-slate-700">{{ $o->order_code }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($o->patient->user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-800">{{ $o->patient->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">₹{{ number_format($o->total, 0) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $colors[0] }} {{ $colors[1] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $colors[2] }}"></span>
                                    {{ __(ucfirst($o->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">{{ $o->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.pharmacy.orders.update', $o->id) }}" class="flex items-center justify-end gap-1.5">
                                    @csrf @method('PATCH')
                                    <select name="status" class="h-8 px-2 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 focus:border-emerald-500 focus:ring-0 w-36">
                                        @foreach(['placed','packed','shipped','delivered','cancelled'] as $s)
                                            <option value="{{ $s }}" @selected($o->status===$s)>{{ __(ucfirst($s)) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="h-8 w-8 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition flex items-center justify-center" title="{{ __('Save') }}">
                                        <x-icon name="check" class="h-4 w-4"/>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="inline-flex flex-col items-center gap-2 text-slate-400">
                                    <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                        <x-icon name="cart" class="h-6 w-6"/>
                                    </div>
                                    <p class="text-sm font-medium">{{ __('No recent orders.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
