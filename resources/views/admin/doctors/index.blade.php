@extends('layouts.app')
@section('title', __('Doctors'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><x-icon name="stethoscope" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Doctors') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Manage doctors in the network.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Pill-box filter style --}}
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11 px-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('Search name or email') }}"
                           class="h-10 px-1 border-0 focus:ring-0 text-sm font-medium text-slate-700 w-52 bg-transparent">
                </div>
                <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Search') }}</button>
                @if($q)
                    <a href="{{ route('admin.doctors.index') }}"
                       class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                        <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.doctors.create') }}" class="inline-flex items-center gap-1.5 h-11 px-4 rounded-lg bg-teal-600 text-white text-sm font-bold hover:bg-teal-700 shadow-sm"><x-icon name="plus" class="h-4 w-4"/> {{ __('Add Doctor') }}</a>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Speciality') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Experience') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Fee') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Listing') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($doctors as $d)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-3">
                            <div class="font-semibold">Dr. {{ $d->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $d->user->email }}</div>
                        </td>
                        <td class="px-5 py-3">{{ __($d->specialization->name ?? '—') }}</td>
                        <td class="px-5 py-3">{{ $d->experience_years }} {{ __('yrs') }}</td>
                        <td class="px-5 py-3">₹{{ number_format($d->consultation_fee,0) }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $d->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $d->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <form method="POST" action="{{ route('admin.doctors.toggle',$d->id) }}" class="inline">@csrf @method('PATCH')
                                    <button class="text-xs font-semibold text-teal-600 hover:underline">{{ $d->is_active ? __('Hide') : __('Show') }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.doctors.toggle-active',$d->id) }}" class="inline"
                                      onsubmit="return confirm('{{ $d->user->is_active ? __('Deactivate this doctor account?') : __('Activate this doctor account?') }}')">
                                    @csrf @method('PATCH')
                                    <button class="text-xs font-semibold hover:underline {{ $d->user->is_active ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $d->user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">{{ __('No doctors yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50">{{ $doctors->links() }}</div>
    </div>
</div>
@endsection
