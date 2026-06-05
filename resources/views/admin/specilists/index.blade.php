@extends('layouts.app')
@section('title', __('Specilists'))
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Heading --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                <x-icon name="stethoscope" class="h-6 w-6"/>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ __('Specilist Management') }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('Add and manage specilist categories shown on the site.') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Pill-box filter style --}}
            <form method="GET" class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1.5 bg-white rounded-lg border border-slate-200 shadow-sm px-3.5 h-11">
                    <x-icon name="search" class="h-4 w-4 text-slate-400"/>
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Search name') }}"
                           class="h-10 px-1 border-0 focus:ring-0 text-xs font-medium text-slate-700 w-44 bg-transparent">
                </div>
                <div class="bg-white rounded-lg border border-slate-200 px-2 h-9 flex items-center">
                    <select name="status" class="h-10 px-1 border-0 focus:ring-0 bg-transparent text-xs font-medium text-slate-700 w-32">
                        <option value="">{{ __('All status') }}</option>
                        <option value="1" @selected(($status ?? '') === '1')>{{ __('Active') }}</option>
                        <option value="0" @selected(($status ?? '') === '0')>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <button class="h-11 px-5 rounded-lg bg-teal-600 text-white font-bold text-sm hover:bg-teal-700 transition shadow-sm">{{ __('Filter') }}</button>
                @if(!empty($q) || ($status ?? '') !== '')
                    <a href="{{ route('admin.specilists.index') }}"
                       class="h-11 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 transition">
                        <x-icon name="x" class="h-3.5 w-3.5"/> {{ __('Reset') }}
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="grid lg:grid-cols-[380px_1fr] gap-6">

        {{-- ── LEFT: Add form ── --}}
        <form action="{{ route('admin.specilists.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3 h-fit">
            @csrf
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <x-icon name="plus" class="h-4 w-4 text-violet-600"/>
                {{ __('Add Specilist') }}
            </h3>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Name') }}</label>
                <input type="text" name="name" required placeholder="{{ __('e.g. Cardiologist') }}"
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-violet-500 focus:ring-violet-500">
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Icon') }}</label>
                <input type="file" name="icon" id="iconInput" accept="image/*"
                       class="mt-1 w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                <img id="previewIcon" alt="" class="hidden mt-3 h-20 w-20 rounded-xl object-cover border border-slate-200 p-2 bg-slate-50">
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Status') }}</label>
                <select name="status" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full mt-2 px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700 transition flex items-center justify-center gap-2">
                <x-icon name="check" class="h-4 w-4"/> {{ __('Add Specilist') }}
            </button>
        </form>

        {{-- ── RIGHT: List table ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">{{ __('Specilists List') }}</h3>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-2.5 py-1 rounded-md">
                    {{ trans_choice('{1} :count specilist|[2,*] :count specilists', $specilists->count(), ['count' => $specilists->count()]) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3 text-left">{{ __('Icon') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($specilists as $sp)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-3">
                                    @if($sp->icon)
                                        <img src="{{ asset('assets/specilist/'.$sp->icon) }}"
                                             class="h-12 w-12 rounded-lg object-contain border border-slate-200 p-1 bg-slate-50">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400">
                                            <x-icon name="stethoscope" class="h-5 w-5"/>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $sp->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sp->status == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $sp->status == 1 ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="openEditModal('{{ $sp->id }}', @js($sp->name), '{{ $sp->status }}', '{{ $sp->icon ? asset('assets/specilist/'.$sp->icon) : '' }}')"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition"
                                                title="{{ __('Edit') }}">
                                            <x-icon name="edit" class="h-4 w-4"/>
                                        </button>
                                        <form action="{{ route('admin.specilists.delete', $sp->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('{{ __('Delete this specilist?') }}')"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition"
                                                    title="{{ __('Delete') }}">
                                                <x-icon name="trash" class="h-4 w-4"/>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center">
                                    <div class="inline-flex flex-col items-center gap-2 text-slate-400">
                                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                                            <x-icon name="stethoscope" class="h-6 w-6"/>
                                        </div>
                                        <p class="text-sm font-medium">{{ __('No specilists added yet') }}</p>
                                        <p class="text-xs">{{ __('Use the form on the left to add your first specilist.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Modal ── --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full md:w-1/2 max-w-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <x-icon name="edit" class="h-5 w-5"/>
                </div>
                <h2 class="text-lg font-extrabold text-slate-900">{{ __('Edit Specilist') }}</h2>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition">
                <x-icon name="x" class="h-5 w-5"/>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-3">
            @csrf

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Name') }}</label>
                <input type="text" name="name" id="editName" required
                       class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-violet-500 focus:ring-violet-500">
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Replace Icon') }}</label>
                <div class="mt-1 flex items-center gap-3">
                    <img id="editPreview" alt="" class="hidden h-16 w-16 rounded-lg object-contain border border-slate-200 p-1 bg-slate-50">
                    <input type="file" name="icon" accept="image/*"
                           class="flex-1 text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">{{ __('Leave blank to keep current icon.') }}</p>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ __('Status') }}</label>
                <select name="status" id="editStatus" class="mt-1 w-full rounded-lg border-slate-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700 transition flex items-center gap-2">
                    <x-icon name="check" class="h-4 w-4"/> {{ __('Update Specilist') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Image preview for Add form
    document.getElementById('iconInput')?.addEventListener('change', function (e) {
        const img = document.getElementById('previewIcon');
        const file = e.target.files[0];
        if (file) {
            img.src = URL.createObjectURL(file);
            img.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
        }
    });

    function openEditModal(id, name, status, iconUrl) {
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.getElementById('editName').value = name;
        document.getElementById('editStatus').value = status;
        document.getElementById('editForm').action = '/admin/specilists/update/' + id;

        const preview = document.getElementById('editPreview');
        if (iconUrl) {
            preview.src = iconUrl;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close on backdrop click
    document.getElementById('editModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeEditModal();
    });

    // Close on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeEditModal();
    });
</script>
@endsection
