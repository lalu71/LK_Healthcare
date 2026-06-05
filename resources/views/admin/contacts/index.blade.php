@extends('layouts.app')
@section('title', __('Contact Messages'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center"><x-icon name="mail" class="h-6 w-6"/></div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Messages') }}</h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Pill-box filter style --}}
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11 px-2">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Name, email, subject') }}"
                           class="h-10 px-1 border-0 focus:ring-0 text-xs font-medium text-slate-700 w-48 bg-transparent">
                </div>
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm px-2 h-11 flex items-center">
                    <select name="status" class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                        <option value="">{{ __('All status') }}</option>
                        <option value="pending" @selected(($status ?? '')==='pending')>{{ __('Pending') }}</option>
                        <option value="handled" @selected(($status ?? '')==='handled')>{{ __('Handled') }}</option>
                    </select>
                </div>
                <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Filter') }}</button>
                @if(!empty($q) || !empty($status))
                    <a href="{{ route('admin.contacts.index') }}"
                       class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                        <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                    </a>
                @endif
            </form>
            @if($messages->count() > 0)
                <form method="POST" action="{{ route('admin.contacts.destroyAll') }}" onsubmit="return confirm('{{ __('Are you sure you want to delete all messages?') }}');">
                    @csrf @method('DELETE')
                    <button type="submit" class="h-9 px-3 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5">
                        <x-icon name="trash" class="h-4 w-4"/> {{ __('Delete All') }}
                    </button>
                </form>
            @endif
        </div>
    </div>


    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($messages as $m)
                <div class="p-6 transition-colors hover:bg-slate-50 {{ $m->is_handled ? 'bg-slate-50/50 opacity-75' : 'bg-white' }}">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 h-12 w-12 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-4 mb-1">
                                <h3 class="text-base font-bold text-slate-900 truncate">{{ $m->name }}</h3>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs text-slate-500">{{ $m->created_at->diffForHumans() }}</span>
                                    
                                    <button onclick="document.getElementById('reply-form-{{$m->id}}').classList.toggle('hidden')" class="px-3 py-1 text-xs font-semibold rounded-full border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                        {{ __('Reply') }}
                                    </button>

                                    <form method="POST" action="{{ route('admin.contacts.toggle',$m->id) }}">@csrf @method('PATCH')
                                        <button class="px-3 py-1 text-xs font-semibold rounded-full border transition-colors {{ $m->is_handled ? 'border-slate-300 text-slate-600 hover:bg-slate-200' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                            {{ $m->is_handled ? __('Mark unread') : __('Mark handled') }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.contacts.destroy', $m->id) }}" onsubmit="return confirm('{{ __('Delete this message?') }}');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 transition-colors" title="{{ __('Delete message') }}">
                                            <x-icon name="trash" class="h-5 w-5"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-500 mb-3">
                                <span class="inline-flex items-center gap-1"><x-icon name="mail" class="h-4 w-4"/> {{ $m->email }}</span>
                                @if($m->phone)<span class="inline-flex items-center gap-1"><x-icon name="phone" class="h-4 w-4"/> {{ $m->phone }}</span>@endif
                            </div>
                            
                            @if($m->subject)
                                <h4 class="text-sm font-semibold text-slate-800 mb-1">{{ __('Subject') }}: {{ $m->subject }}</h4>
                            @endif
                            <div class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $m->message }}
                            </div>

                            {{-- Reply Form --}}
                            <div id="reply-form-{{$m->id}}" class="hidden mt-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                                <form method="POST" action="{{ route('admin.contacts.reply', $m->id) }}">
                                    @csrf
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Send a reply to') }} {{ $m->email }}</label>
                                    <textarea name="reply_message" required rows="4" class="w-full rounded-lg border-slate-300 focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="{{ __('Type your reply here...') }}"></textarea>
                                    <div class="mt-3 flex justify-end gap-2">
                                        <button type="button" onclick="document.getElementById('reply-form-{{$m->id}}').classList.add('hidden')" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">{{ __('Cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-lg shadow-sm transition-colors flex items-center gap-2"><x-icon name="paper-airplane" class="h-4 w-4"/> {{ __('Send Reply') }}</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-500">
                    <x-icon name="mail" class="h-10 w-10 mx-auto text-slate-300 mb-3"/>
                    {{ __('No messages found.') }}
                </div>
            @endforelse
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
