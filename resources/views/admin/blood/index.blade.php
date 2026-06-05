@extends('layouts.app')
@section('title', __('Blood Bank'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><x-icon name="droplet" class="h-6 w-6"/></div>
        <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Blood Bank Management') }}</h1>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold mb-3">{{ __('Inventory') }}</h3>
            <form method="POST" action="{{ route('admin.blood.inventory.update') }}" class="flex items-center gap-2 mb-4">
                @csrf
                <select name="blood_group" class="rounded-lg border-slate-300 text-sm">
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)<option>{{ $bg }}</option>@endforeach
                </select>
                <input type="number" name="units" min="0" required class="rounded-lg border-slate-300 text-sm w-24">
                <button class="px-3 py-2 rounded-lg bg-rose-600 text-white text-xs font-semibold">{{ __('Update') }}</button>
            </form>
            <div class="grid grid-cols-4 gap-2">
                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                    @php $u = optional($inventory->firstWhere('blood_group',$bg))->units ?? 0; @endphp
                    <div class="rounded-xl bg-slate-50 p-3 text-center">
                        <div class="text-xl font-extrabold text-rose-600">{{ $bg }}</div>
                        <div class="text-xs font-semibold">{{ $u }} {{ __('units') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-bold mb-3">{{ __('Requests') }}</h3>
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse($requests as $r)
                    <div class="p-3 rounded-lg border border-slate-200 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold">{{ $r->blood_group }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm">{{ $r->patient_name }} · {{ $r->units }} {{ __('units') }}</div>
                            <div class="text-xs text-slate-500">{{ $r->contact_phone }} · {{ $r->hospital ?? '-' }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.blood.requests.update',$r->id) }}">@csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-xs rounded border-slate-300">
                                @foreach(['pending','fulfilled','cancelled'] as $s)<option value="{{ $s }}" @selected($r->status===$s)>{{ __(ucfirst($s)) }}</option>@endforeach
                            </select>
                        </form>
                    </div>
                @empty
                    <div class="text-sm text-slate-400">{{ __('No requests.') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold mb-3">{{ __('Registered Donors') }}</h3>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr><th class="p-2 text-left">{{ __('Name') }}</th><th class="p-2 text-left">{{ __('Group') }}</th><th class="p-2 text-left">{{ __('Phone') }}</th><th class="p-2 text-left">{{ __('City') }}</th><th class="p-2 text-left">{{ __('Last Donated') }}</th></tr></thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($donors as $d)
                    <tr>
                        <td class="p-2">{{ $d->name }}</td>
                        <td class="p-2 font-bold text-rose-600">{{ $d->blood_group }}</td>
                        <td class="p-2">{{ $d->phone }}</td>
                        <td class="p-2">{{ $d->city ?? '-' }}</td>
                        <td class="p-2 text-slate-500">{{ optional($d->last_donated_at)->format('d M Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
