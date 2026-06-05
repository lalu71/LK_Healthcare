<x-guest-layout>
    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Create account') }}</h2>
    <p class="mt-2 text-slate-500 text-sm">{{ __('Start your LK Healthcare journey in under a minute.') }}</p>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="name" class="text-sm font-semibold text-slate-700">{{ __('Full name') }}</label>
            <input id="name" name="name" type="text" required autofocus value="{{ old('name') }}"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}" autocomplete="username"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="phone" class="text-sm font-semibold text-slate-700">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">{{ __('Profile Picture') }}</label>
                <div x-data="{
                        name: '',
                        preview: null,
                        pick(e) {
                            const f = e.target.files[0];
                            if (!f) { this.name=''; this.preview=null; return; }
                            this.name = f.name;
                            const r = new FileReader();
                            r.onload = ev => this.preview = ev.target.result;
                            r.readAsDataURL(f);
                        },
                        clear() {
                            this.name=''; this.preview=null;
                            document.getElementById('avatar').value='';
                        }
                     }"
                     class="mt-1 flex items-center gap-3">
                    <div class="relative shrink-0 h-12 w-12 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-slate-400">
                        <template x-if="preview">
                            <img :src="preview" class="h-full w-full object-cover" alt="">
                        </template>
                        <template x-if="!preview">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0v.75H4.5v-.75z"/>
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="avatar" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 text-xs font-semibold border border-teal-200 hover:bg-teal-100 cursor-pointer transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 7.5m0 0L7.5 12M12 7.5v9"/>
                            </svg>
                            <span x-text="name ? '{{ __('Change') }}' : '{{ __('Choose photo') }}'"></span>
                        </label>
                        <button type="button" x-show="name" @click="clear()" x-cloak
                                class="ml-1 text-[11px] text-rose-500 hover:underline">{{ __('Remove') }}</button>
                        <p class="mt-1 text-[11px] text-slate-400 truncate"
                           x-text="name || '{{ __('PNG, JPG up to 2 MB') }}'"></p>
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden" @change="pick($event)">
                    </div>
                </div>
                @error('avatar')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="text-sm font-semibold text-slate-700">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
                @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="text-sm font-semibold text-slate-700">{{ __('Confirm') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            </div>
        </div>
        <button type="submit" class="w-full px-4 py-3 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700 shadow-md shadow-teal-100">
            {{ __('Create Account') }}
        </button>
    </form>

    <p class="mt-8 text-sm text-slate-500 text-center">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="text-teal-600 font-semibold hover:underline">{{ __('Sign in') }}</a>
    </p>
</x-guest-layout>
