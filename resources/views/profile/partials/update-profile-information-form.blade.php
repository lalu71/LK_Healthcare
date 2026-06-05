<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label :value="__('Profile Picture')" />
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
                    cancel() {
                        this.name=''; this.preview=null;
                        document.getElementById('avatar').value='';
                    }
                 }"
                 class="mt-1 flex items-center gap-4">
                <div class="relative shrink-0 h-16 w-16 rounded-full bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center text-slate-400">
                    <template x-if="preview">
                        <img :src="preview" class="h-full w-full object-cover" alt="">
                    </template>
                    <template x-if="!preview">
                        @if($user->avatar)
                            <img src="{{ asset($user->avatar) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                        @else
                            <span class="text-lg font-bold text-teal-600 bg-gradient-to-br from-teal-100 to-emerald-100 h-full w-full flex items-center justify-center">{{ strtoupper(substr($user->name,0,1)) }}</span>
                        @endif
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <label for="avatar" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 text-xs font-semibold border border-teal-200 hover:bg-teal-100 cursor-pointer transition">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 7.5m0 0L7.5 12M12 7.5v9"/>
                        </svg>
                        <span x-text="name ? '{{ __('Change') }}' : ('{{ $user->avatar ? __('Replace photo') : __('Upload photo') }}')"></span>
                    </label>
                    @if($user->avatar)
                        <label class="ml-1 inline-flex items-center gap-1 text-[11px] text-rose-500 hover:underline cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-rose-300 text-rose-500 focus:ring-rose-400 h-3 w-3">
                            {{ __('Remove current') }}
                        </label>
                    @endif
                    <button type="button" x-show="name" @click="cancel()" x-cloak
                            class="ml-1 text-[11px] text-slate-500 hover:underline">{{ __('Cancel') }}</button>
                    <p class="mt-1 text-[11px] text-slate-400 truncate"
                       x-text="name || '{{ __('PNG, JPG up to 2 MB') }}'"></p>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden" @change="pick($event)">
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
