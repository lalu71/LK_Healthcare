@php
    $notifications = auth()->user()->appNotifications()->limit(8)->get();
    $unread = auth()->user()->appNotifications()->whereNull('read_at')->count();
    $isAdmin = auth()->user()->hasRole('admin');
    $siteDown = $isAdmin ? \App\Models\Setting::isShutdown() : false;
@endphp

<header class="sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button @click="sidebar=true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                <x-icon name="menu" class="h-6 w-6"/>
            </button>
            <div class="hidden md:flex items-center gap-2 text-slate-500 text-sm">
                <x-icon name="calendar" class="h-4 w-4"/>
                <span>{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- site status (admin) --}}
            @if($isAdmin)
                <form method="POST" action="{{ route('admin.site.toggle-shutdown') }}"
                      onsubmit="return confirm('{{ $siteDown ? __('Activate the site?') : __('Shut down the site? Non-admin actions will be blocked.') }}')"
                      class="hidden sm:inline-flex items-center gap-2 rounded-lg border px-2 py-1.5 {{ $siteDown ? 'bg-rose-50 border-rose-200' : 'bg-emerald-50 border-emerald-200' }}">
                    @csrf
                    <span class="flex items-center gap-1.5 pl-1">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $siteDown ? 'bg-rose-400' : 'bg-emerald-400' }}"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $siteDown ? 'bg-rose-600' : 'bg-emerald-600' }}"></span>
                        </span>
                        <span class="text-[11px] font-extrabold uppercase tracking-wide {{ $siteDown ? 'text-rose-700' : 'text-emerald-700' }}">{{ $siteDown ? __('Down') : __('Active') }}</span>
                    </span>
                    <button class="px-2.5 py-1 rounded-md text-[11px] font-extrabold inline-flex items-center gap-1 transition {{ $siteDown ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-600 text-white hover:bg-rose-700' }}"
                            title="{{ $siteDown ? __('Activate the site') : __('Shut down the site') }}">
                        <x-icon name="{{ $siteDown ? 'check' : 'x' }}" class="h-3 w-3"/>
                        {{ $siteDown ? __('Activate') : __('Shut Down') }}
                    </button>
                </form>
            @endif

            {{-- language --}}
            <div x-data="{ open:false }" class="relative">
                <button @click="open=!open" class="flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-100 text-sm font-medium text-slate-600">
                    <x-icon name="globe" class="h-5 w-5"/>
                    <span class="hidden sm:inline uppercase">{{ app()->getLocale() }}</span>
                </button>
                <div x-show="open" @click.outside="open=false" x-transition x-cloak class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg ring-1 ring-slate-200 overflow-hidden">
                    <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 {{ app()->getLocale()=='en'?'text-teal-600 font-semibold':'text-slate-700' }}">English</a>
                    <a href="{{ route('lang.switch', 'hi') }}" class="block px-4 py-2 text-sm hover:bg-slate-50 {{ app()->getLocale()=='hi'?'text-teal-600 font-semibold':'text-slate-700' }}">हिन्दी</a>
                </div>
            </div>

            {{-- notifications --}}
            <div x-data="{ open:false }" class="relative">
                <button @click="open=!open" class="relative p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                    <x-icon name="bell" class="h-5 w-5"/>
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 inline-flex items-center justify-center h-4 min-w-4 px-1 text-[10px] font-bold rounded-full bg-rose-500 text-white">{{ $unread }}</span>
                    @endif
                </button>
                <div x-show="open" @click.outside="open=false" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg ring-1 ring-slate-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <span class="font-semibold text-slate-700">{{ __('Notifications') }}</span>
                        @if($unread > 0)
                            <form action="{{ route('notifications.read_all') }}" method="POST">@csrf
                                <button class="text-xs text-teal-600 hover:underline">{{ __('Mark all read') }}</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                        @forelse($notifications as $n)
                            <a href="{{ $n->link ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 {{ $n->read_at ? '' : 'bg-teal-50/50' }}">
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 h-8 w-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                                        <x-icon :name="$n->icon ?? 'bell'" class="h-4 w-4"/>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">{{ __($n->title) }}</p>
                                        @if($n->body) <p class="text-xs text-slate-500 truncate">{{ __($n->body) }}</p>@endif
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $n->created_at->translatedFormat('d M Y, h:i A') }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No notifications yet') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- profile --}}
            <div x-data="{ open:false }" class="relative">
                <button @click="open=!open" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-slate-100">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover ring-1 ring-slate-200">
                    @else
                        <span class="h-8 w-8 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center font-bold text-sm">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    @endif
                    <div class="hidden sm:block text-left leading-tight">
                        <div class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 uppercase tracking-wide">{{ auth()->user()->roles->first()->name ?? 'patient' }}</div>
                    </div>
                    <x-icon name="chevron-down" class="h-4 w-4 text-slate-400"/>
                </button>
                <div x-show="open" @click.outside="open=false" x-transition x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg ring-1 ring-slate-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-200">
                        <div class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="user" class="h-4 w-4"/> {{ __('Profile') }}</a>
                    @auth
                        @if(auth()->user()->hasRole('patient'))
                            <a href="{{ route('patient.profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50"><x-icon name="edit" class="h-4 w-4"/> {{ __('Medical Profile') }}</a>
                        @endif
                    @endauth
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50"><x-icon name="logout" class="h-4 w-4"/> {{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
