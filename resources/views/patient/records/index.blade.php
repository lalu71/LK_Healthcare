@extends('layouts.app')
@section('title', __('Medical Records'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><x-icon name="file" class="h-6 w-6"/></div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Medical Records') }}</h1>
                <p class="text-slate-500 text-sm">{{ __('Upload lab reports, X-rays, prescriptions.') }}</p>
            </div>
        </div>
        <x-list-filter :action="route('patient.records.index')" :q="$q" :placeholder="__('Search title')" :hasFilters="!empty($q) || !empty($type)">
            <select name="type" class="h-11 rounded-lg border-slate-200 text-sm font-medium text-slate-700 focus:ring-teal-500 focus:border-teal-500">
                <option value="">{{ __('All types') }}</option>
                <option value="lab" @selected(($type ?? '')==='lab')>{{ __('Lab Report') }}</option>
                <option value="xray" @selected(($type ?? '')==='xray')>X-Ray</option>
                <option value="mri" @selected(($type ?? '')==='mri')>MRI / CT</option>
                <option value="prescription" @selected(($type ?? '')==='prescription')>{{ __('Prescription') }}</option>
                <option value="other" @selected(($type ?? '')==='other')>{{ __('Other') }}</option>
            </select>
        </x-list-filter>
    </div>

    <div class="grid lg:grid-cols-[380px_1fr] gap-6">
        <form method="POST" action="{{ route('patient.records.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4 h-fit lg:sticky lg:top-20">
            @csrf
            <h3 class="font-bold text-slate-900">{{ __('Upload Record') }}</h3>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Title') }}</label>
                <input name="title" required class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Type') }}</label>
                <select name="type" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
                    <option value="lab">{{ __('Lab Report') }}</option>
                    <option value="xray">X-Ray</option>
                    <option value="mri">MRI / CT</option>
                    <option value="prescription">{{ __('Prescription') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Record date') }}</label>
                <input type="date" name="record_date" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('File (PDF/Image, max 10MB)') }}</label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-1 w-full text-sm">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Description') }}</label>
                <textarea name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>
            <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700"><x-icon name="upload" class="h-5 w-5"/> {{ __('Upload') }}</button>
        </form>

        <div>
            @if($records->isEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                    <x-icon name="file" class="h-14 w-14 mx-auto text-slate-300"/>
                    <p class="mt-3 text-slate-500">{{ __('No records found.') }}</p>
                </div>
            @else
                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach($records as $r)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition">
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                    <x-icon name="file" class="h-5 w-5"/>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $r->title }}</div>
                                    <div class="text-xs text-slate-500">{{ __($r->type) }} · {{ $r->created_at->translatedFormat('d M Y') }}</div>
                                    @if($r->description)<p class="mt-2 text-xs text-slate-600">{{ $r->description }}</p>@endif
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <a href="{{ route('patient.records.download',$r->id) }}" class="text-xs font-semibold text-teal-600 hover:underline inline-flex items-center gap-1"><x-icon name="download" class="h-4 w-4"/> {{ __('Download') }}</a>
                                <form method="POST" action="{{ route('patient.records.destroy',$r->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                                    <button class="text-xs font-semibold text-rose-600 hover:underline inline-flex items-center gap-1"><x-icon name="trash" class="h-4 w-4"/> {{ __('Delete') }}</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">{{ $records->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
