@props(['status'])

@if ($status)
    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 3000)" {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-200 flex items-center justify-between gap-3']) }}>
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ $status }}
        </div>
        <button type="button" @click="show = false" class="text-green-500 hover:text-green-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
