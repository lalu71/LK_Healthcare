@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin');
    $isDoctor = $user?->hasRole('doctor');
    $isPharmacist = $user?->hasRole('pharmacist');
    $isPatient = $user?->hasRole('patient') || (!$isAdmin && !$isDoctor && !$isPharmacist);
    $siteContent = \App\Models\SiteContent::first();
@endphp

<aside
    :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 lg:translate-x-0">

    <div class="h-20 flex items-center px-5 border-b border-slate-200">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <span class="inline-flex h-14 w-14 items-center justify-center overflow-hidden"><img src="{{ asset('assets/site_images/lklogo.png') }}"  alt="LK Healthcare Logo"  class="max-h-full max-w-full object-contain"></span>
            <span class="font-extrabold tracking-tight text-slate-900 text-xl">Healthcare</span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">

        <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Main Menu') }}</div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') || request()->routeIs('pharmacist.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
            <x-icon name="home" class="h-5 w-5"/> {{ __('Dashboard') }}
        </a>

        {{-- PATIENT --}}
        @if($isPatient)
            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Healthcare') }}</div>

            <a href="{{ route('patient.book') }}" class="sidebar-link {{ request()->routeIs('patient.book*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="calendar" class="h-5 w-5"/> {{ __('Book Appointment') }}
            </a>
            <a href="{{ route('patient.appointments.index') }}" class="sidebar-link {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="clock" class="h-5 w-5"/> {{ __('My Appointments') }}
            </a>
            <a href="{{ route('patient.prescriptions.index') }}" class="sidebar-link {{ request()->routeIs('patient.prescriptions.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="pill" class="h-5 w-5"/> {{ __('Prescriptions') }}
            </a>
            <a href="{{ route('patient.records.index') }}" class="sidebar-link {{ request()->routeIs('patient.records.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="file" class="h-5 w-5"/> {{ __('Medical Records') }}
            </a>

            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Services') }}</div>

            <a href="{{ route('patient.lab.index') }}" class="sidebar-link {{ request()->routeIs('patient.lab.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="flask" class="h-5 w-5"/> {{ __('Lab Tests') }}
            </a>
            <a href="{{ route('patient.pharmacy.index') }}" class="sidebar-link {{ request()->routeIs('patient.pharmacy.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="cart" class="h-5 w-5"/> {{ __('Pharmacy') }}
            </a>
            <a href="{{ route('blood.index') }}" class="sidebar-link {{ request()->routeIs('blood.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="droplet" class="h-5 w-5"/> {{ __('Blood Bank') }}
            </a>
            <a href="{{ route('emergency.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-amber-50 text-amber-800 hover:bg-amber-100 font-semibold border border-amber-200">
                <x-icon name="ambulance" class="h-5 w-5"/> {{ __('Emergency') }}
            </a>
        @endif

        {{-- DOCTOR --}}
        @if($isDoctor)
            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Practice') }}</div>

            <a href="{{ route('doctor.appointments.index') }}" class="sidebar-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="calendar" class="h-5 w-5"/> {{ __('Appointments') }}
            </a>
            <a href="{{ route('doctor.availability.index') }}" class="sidebar-link {{ request()->routeIs('doctor.availability.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="clock" class="h-5 w-5"/> {{ __('Availability') }}
            </a>
            <a href="{{ route('doctor.prescriptions.index') }}" class="sidebar-link {{ request()->routeIs('doctor.prescriptions.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="pill" class="h-5 w-5"/> {{ __('Prescriptions') }}
            </a>
            <a href="{{ route('doctor.profile.edit') }}" class="sidebar-link {{ request()->routeIs('doctor.profile.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="stethoscope" class="h-5 w-5"/> {{ __('My Profile') }}
            </a>
        @endif

        {{-- PHARMACIST --}}
        @if($isPharmacist)
            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Pharmacy Store') }}</div>

            <a href="{{ route('pharmacist.prescriptions.index') }}" class="sidebar-link {{ request()->routeIs('pharmacist.prescriptions.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="pill" class="h-5 w-5"/> {{ __('Prescription Requests') }}
            </a>
            <a href="{{ route('pharmacist.inventory.index') }}" class="sidebar-link {{ request()->routeIs('pharmacist.inventory.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="cart" class="h-5 w-5"/> {{ __('Inventory Management') }}
            </a>
        @endif

        {{-- ADMIN --}}
        @if($isAdmin)
            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Management') }}</div>

            <a href="{{ route('admin.patients.index') }}" class="sidebar-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="users" class="h-5 w-5"/> {{ __('Patients') }}
            </a>
            <a href="{{ route('admin.doctors.index') }}" class="sidebar-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="stethoscope" class="h-5 w-5"/> {{ __('Doctors') }}
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="sidebar-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="calendar" class="h-5 w-5"/> {{ __('Appointments') }}
            </a>

            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Inventory') }}</div>

            <a href="{{ route('admin.lab.index') }}" class="sidebar-link {{ request()->routeIs('admin.lab.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="flask" class="h-5 w-5"/> {{ __('Lab Tests') }}
            </a>
            <a href="{{ route('admin.pharmacy.index') }}" class="sidebar-link {{ request()->routeIs('admin.pharmacy.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="pill" class="h-5 w-5"/> {{ __('Pharmacy') }}
            </a>
            <a href="{{ route('admin.blood.index') }}" class="sidebar-link {{ request()->routeIs('admin.blood.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="droplet" class="h-5 w-5"/> {{ __('Blood Bank') }}
            </a>
            <a href="{{ route('admin.emergency.index') }}" class="sidebar-link {{ request()->routeIs('admin.emergency.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="ambulance" class="h-5 w-5"/> {{ __('Emergency') }}
            </a>
            <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="mail" class="h-5 w-5"/> {{ __('Messages') }}
            </a>
            <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="chat" class="h-5 w-5"/> {{ __('Reviews') }}
            </a>
            <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="shield-check" class="h-5 w-5"/> {{ __('Services') }}
            </a>
            <a href="{{ route('admin.specilists.index') }}" class="sidebar-link {{ request()->routeIs('admin.specilists.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
                <x-icon name="stethoscope" class="h-5 w-5"/> {{ __('Specilists') }}
            </a>
        @endif

        <div class="pt-3 pb-1 px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Account') }}</div>
        @if($isAdmin)
        <a href="{{ route('admin.lk_site_content') }}" class="sidebar-link {{ request()->routeIs('admin.lk_site_content') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
            <x-icon name="cog" class="h-5 w-5"/> {{ __('Settings') }}
        </a>
        @else
        <a href="{{ route('reviews.create') }}" class="sidebar-link {{ request()->routeIs('reviews.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 font-medium">
            <x-icon name="chat" class="h-5 w-5"/> {{ __('Rate & Review') }}
        </a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-rose-50 hover:text-rose-700 font-medium">
                <x-icon name="logout" class="h-5 w-5"/> {{ __('Logout') }}
            </button>
        </form>
    </nav>

    <div class="p-4 mt-auto border-t border-slate-100">
        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
            <div class="text-xs font-bold text-slate-900 mb-1">{{ __('Need help?') }}</div>
            <p class="text-[10px] text-slate-500 leading-tight mb-2">{{ __('Our team is available 24/7 for support.') }}</p>
            <div class="text-teal-600 font-bold text-xs">+91 {{ $siteContent->help_contact ?? '7084275768' }}</div>
        </div>
    </div>
</aside>
