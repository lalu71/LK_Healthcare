@extends('layouts.app')
@section('title', __('Site Content'))
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Heading --}}
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <x-icon name="cog" class="h-6 w-6"/>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Site Content') }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('Update your website content information.') }}</p>
        </div>
    </div>

    <form action="{{ route('admin.update_site_content') }}" method="POST"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Site Name --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Site Name') }}</label>
                <input type="text" name="site_name" value="{{ old('site_name', $sitecontent->site_name ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('site_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Site Title --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Site Title') }}</label>
                <input type="text" name="site_title" value="{{ old('site_title', $sitecontent->site_title ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('site_title')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Help Contact --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Help Contact') }}</label>
                <input type="text" name="help_contact" value="{{ old('help_contact', $sitecontent->help_contact ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('help_contact')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Follow By --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Follow By') }}</label>
                <input type="text" name="follow_by" value="{{ old('follow_by', $sitecontent->follow_by ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('follow_by')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Site Email') }}</label>
                <input type="email" name="site_email" value="{{ old('site_email', $sitecontent->site_email ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('site_email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Address --}}
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Site Address') }}</label>
                <input type="text" name="site_address" value="{{ old('site_address', $sitecontent->site_address ?? '') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('site_address')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

        </div>

        {{-- Description --}}
        <div>
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Site Description') }}</label>
            <textarea name="site_description" rows="5"
                      class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('site_description', $sitecontent->site_description ?? '') }}</textarea>
            @error('site_description')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Submit --}}
        <div class="flex justify-end pt-2 border-t border-slate-100">
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 transition flex items-center gap-2">
                <x-icon name="check" class="h-4 w-4"/> {{ __('Save Changes') }}
            </button>
        </div>

    </form>

</div>
@endsection
