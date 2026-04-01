@props([
    'message',
    'type' => 'success',
    'timeout' => 5000,
])

@php
    $isError = $type === 'error';

    $containerClasses = $isError
        ? 'mb-6 flex items-center justify-between rounded-lg border-l-4 border-red-500 bg-red-100 px-4 py-3 text-red-800 shadow-sm'
        : 'mb-6 flex items-center justify-between rounded-lg border-l-4 border-green-500 bg-green-100 px-4 py-3 text-green-800 shadow-sm';

    $iconClasses = $isError ? 'h-5 w-5 text-red-500' : 'h-5 w-5 text-green-500';
    $closeClasses = $isError
        ? 'text-red-500 transition-colors hover:text-red-700'
        : 'text-green-500 transition-colors hover:text-green-700';
@endphp

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ (int) $timeout }})"
    x-show="show"
    x-transition.opacity.duration.250ms
    {{ $attributes->merge(['class' => $containerClasses]) }}
>
    <div class="flex items-center gap-2.5">
        @if($isError)
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 9v4"></path>
                <path d="M12 17h.01"></path>
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
            </svg>
        @else
            <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        @endif

        <span class="text-sm font-medium leading-5">{{ $message }}</span>
    </div>

    <button type="button" @click="show = false" class="{{ $closeClasses }}" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 6L6 18M6 6l12 12"></path>
        </svg>
    </button>
</div>
