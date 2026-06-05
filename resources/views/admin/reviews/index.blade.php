@extends('layouts.app')
@section('title', __('Reviews'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Heading --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <x-icon name="chat" class="h-6 w-6"/>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Site Reviews') }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Approve reviews to show them on the home page.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if($pendingCount > 0)
                <span class="text-[11px] font-bold text-amber-700 bg-amber-100 px-3 py-1.5 rounded-lg">
                    {{ $pendingCount }} {{ __('pending') }}
                </span>
            @endif
            <form method="GET" class="bg-white rounded-lg border border-slate-200 px-2 h-9 flex items-center">
                <select name="status" onchange="this.form.submit()"
                        class="h-8 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                    <option value="">{{ __('All reviews') }}</option>
                    <option value="1" @selected(($status ?? '') === '1')>{{ __('Approved') }}</option>
                    <option value="0" @selected(($status ?? '') === '0')>{{ __('Pending') }}</option>
                </select>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">{{ __('User') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Rating') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Remark') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold shrink-0">
                                        {{ substr($review->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $review->user->name ?? __('Deleted user') }}</div>
                                        <div class="text-xs text-slate-400">{{ $review->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-amber-400">{!! str_repeat('★', $review->rating) !!}<span class="text-slate-300">{!! str_repeat('★', 5 - $review->rating) !!}</span></td>
                            <td class="px-4 py-3 text-slate-600 max-w-md">{{ $review->remark }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $review->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $review->is_approved ? __('Approved') : __('Pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-400 text-xs whitespace-nowrap">{{ $review->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-bold transition {{ $review->is_approved ? 'bg-white border border-slate-200 text-slate-600 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                                                title="{{ $review->is_approved ? __('Hide') : __('Approve') }}">
                                            <x-icon name="{{ $review->is_approved ? 'eye' : 'check' }}" class="h-4 w-4"/>
                                            {{ $review->is_approved ? __('Hide') : __('Approve') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('{{ __('Delete this review?') }}')"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition"
                                                title="{{ __('Delete') }}">
                                            <x-icon name="trash" class="h-4 w-4"/>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="inline-flex flex-col items-center gap-2 text-slate-400">
                                    <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                        <x-icon name="chat" class="h-6 w-6"/>
                                    </div>
                                    <p class="text-sm font-medium">{{ __('No reviews found') }}</p>
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
