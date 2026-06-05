<x-guest-layout>
    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Welcome back') }}</h2>
    <p class="mt-2 text-slate-500 text-sm">{{ __('Sign in to manage your health, appointments and records.') }}</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="email" class="text-sm font-semibold text-slate-700">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" required autofocus value="{{ old('email') }}" autocomplete="username"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-slate-700">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-teal-600 hover:underline font-medium">{{ __('Forgot?') }}</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="mt-1 w-full rounded-lg border-slate-300 focus:ring-teal-500 focus:border-teal-500 py-2.5">
            @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
            {{ __('Remember me') }}
        </label>
        <button type="submit" class="w-full px-4 py-3 rounded-lg bg-teal-600 text-white font-semibold hover:bg-teal-700 shadow-md shadow-teal-100">
            {{ __('Sign In') }}
        </button>
    </form>

    <p class="mt-8 text-sm text-slate-500 text-center">
        {{ __('New to LK Healthcare?') }}
        <a href="{{ route('register') }}" class="text-teal-600 font-semibold hover:underline">{{ __('Create an account') }}</a>
    </p>
</x-guest-layout>
