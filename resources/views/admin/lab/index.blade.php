@extends('layouts.app')
@section('title', __('Lab Management'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center"><x-icon name="flask" class="h-6 w-6"/></div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Lab Management') }}</h1>
        </div>
        <x-list-filter :action="route('admin.lab.index')" :q="$q" :placeholder="__('Search test or category')" :hasFilters="!empty($q)" />
    </div>

    <div class="grid lg:grid-cols-[380px_1fr] gap-6">
        <form method="POST" action="{{ route('admin.lab.tests.store') }}" class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3 h-fit">
            @csrf
            <h3 class="font-bold">{{ __('Add Test') }}</h3>
            <input name="name" required placeholder="{{ __('Test name') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <input name="category" placeholder="{{ __('Category') }}" class="w-full rounded-lg border-slate-300 text-sm">
            <div class="grid grid-cols-2 gap-2">
                <input type="number" name="price" step="0.01" required placeholder="{{ __('Price') }}" class="rounded-lg border-slate-300 text-sm">
                <input type="number" name="duration_hours" required value="24" class="rounded-lg border-slate-300 text-sm">
            </div>
            <textarea name="description" rows="2" placeholder="{{ __('Description') }}" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            <button class="w-full px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700">{{ __('Add Test') }}</button>
        </form>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                    <tr><th class="px-4 py-3 text-left">{{ __('Name') }}</th><th class="px-4 py-3 text-left">{{ __('Category') }}</th><th class="px-4 py-3 text-left">{{ __('Price') }}</th><th class="px-4 py-3 text-left">{{ __('Status') }}</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($tests as $t)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $t->name }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $t->category }}</td>
                            <td class="px-4 py-3">₹{{ number_format($t->price,0) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $t->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $t->is_active ? __('Active') : __('Inactive') }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.lab.tests.toggle',$t->id) }}">@csrf @method('PATCH')<button class="text-xs font-semibold text-teal-600 hover:underline">{{ $t->is_active ? __('Deactivate') : __('Activate') }}</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-50">{{ $tests->links() }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold mb-3">{{ __('Recent Bookings') }}</h3>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-2 text-left">{{ __('Code') }}</th><th class="p-2 text-left">{{ __('Patient') }}</th><th class="p-2 text-left">{{ __('Test') }}</th><th class="p-2 text-left">{{ __('Date') }}</th><th class="p-2 text-left">{{ __('Status') }}</th><th class="p-2 text-left">{{ __('Result') }}</th></tr></thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($bookings as $b)
                    <tr>
                        <td class="p-2 font-mono text-xs">{{ $b->booking_code }}</td>
                        <td class="p-2">{{ $b->patient->user->name }}</td>
                        <td class="p-2">{{ $b->labTest->name }}</td>
                        <td class="p-2">{{ $b->booking_date->format('d M') }}</td>
                        <td class="p-2"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">{{ __(ucfirst($b->status)) }}</span></td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('admin.lab.bookings.result',$b->id) }}" enctype="multipart/form-data" class="flex items-center gap-1">
                                @csrf
                                <input type="file" name="file" required accept=".pdf,.jpg,.png" class="text-xs w-32">
                                <button class="text-xs bg-teal-600 text-white px-2 py-1 rounded">{{ __('Upload') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
