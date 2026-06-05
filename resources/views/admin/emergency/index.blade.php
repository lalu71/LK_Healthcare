@extends('layouts.app')
@section('title', __('Emergency'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><x-icon name="ambulance" class="h-6 w-6"/></div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Emergency Requests') }}</h1>
        </div>
        {{-- Pill-box filter style --}}
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <div class="flex items-center gap-1.5 bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11 px-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Name, phone, location') }}"
                       class="h-10 px-1 border-0 focus:ring-0 text-xs font-medium text-slate-700 w-48 bg-transparent">
            </div>
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-2 h-11 flex items-center">
                <select name="status" class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                    <option value="">{{ __('All status') }}</option>
                    @foreach(['pending','dispatched','arrived','resolved','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(($status ?? '')===$s)>{{ __(ucfirst($s)) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Filter') }}</button>
            @if(!empty($q) || !empty($status))
                <a href="{{ route('admin.emergency.index') }}"
                   class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                    <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                </a>
            @endif
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600">
                <tr><th class="p-3 text-left">#</th><th class="p-3 text-left">{{ __('Contact') }}</th><th class="p-3 text-left">{{ __('Location') }}</th><th class="p-3 text-left">{{ __('Description') }}</th><th class="p-3 text-left">{{ __('Created') }}</th><th class="p-3 text-left">{{ __('Status') }}</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($requests as $r)
                    <tr class="{{ $r->status==='pending' ? 'bg-amber-50/40' : '' }}">
                        <td class="p-3 font-mono text-xs">#{{ $r->id }}</td>
                        <td class="p-3">
                            <div class="font-semibold">{{ $r->contact_name }}</div>
                            <div class="text-xs text-slate-500">{{ $r->contact_phone }}</div>
                        </td>
                        <td class="p-3">
                            <div>{{ $r->location }}</div>
                            @if($r->latitude)<div class="text-xs text-slate-500">{{ $r->latitude }}, {{ $r->longitude }}</div>@endif
                        </td>
                        <td class="p-3 text-slate-600">{{ Str::limit($r->description, 50) ?: '—' }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                        <td class="p-3">
                            <form method="POST" action="{{ route('admin.emergency.update',$r->id) }}">@csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded border-slate-300">
                                    @foreach(['pending','dispatched','arrived','resolved','cancelled'] as $s)<option value="{{ $s }}" @selected($r->status===$s)>{{ __(ucfirst($s)) }}</option>@endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50">{{ $requests->links() }}</div>
    </div>
</div>
@endsection
