@extends('layouts.app')
@section('title', __('Patients'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="users" class="h-6 w-6 text-black"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Patients') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('All registered patients.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('admin.patients.index')" :q="$q" :placeholder="__('Name, email')" :hasFilters="!empty($q)" />
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">{{ __('Name') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Patient ID') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Email') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Blood') }}</th>
                    <th class="px-5 py-3 text-left">{{ __('Registered') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($patients as $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-3 font-semibold">{{ $p->user->name }}</td>
                        <td class="px-5 py-3 font-mono text-xs">{{ $p->patient_id }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $p->user->email }}</td>
                        <td class="px-5 py-3 font-bold text-rose-600">{{ $p->blood_group ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $p->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <form method="POST" action="{{ route('admin.patients.toggle-active', $p->id) }}"
                                      onsubmit="return confirm('{{ $p->user->is_active ? __('Deactivate this patient?') : __('Activate this patient?') }}')">
                                    @csrf @method('PATCH')
                                    <button class="text-xs font-semibold hover:underline {{ $p->user->is_active ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $p->user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.patients.show',$p->id) }}" class="text-teal-600 font-semibold text-xs hover:underline">{{ __('View') }} →</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">{{ __('No patients found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50">{{ $patients->links() }}</div>
    </div>
</div>
@endsection
