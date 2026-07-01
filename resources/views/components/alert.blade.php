@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => true
])

@php
    $typeClasses = [
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    ];

    $iconClasses = [
        'success' => 'text-emerald-500',
        'error' => 'text-red-500',
        'warning' => 'text-amber-500',
        'info' => 'text-blue-500',
    ];

    $classes = $typeClasses[$type] ?? $typeClasses['info'];
    $iconColor = $iconClasses[$type] ?? $iconClasses['info'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 p-4 border rounded-lg animate-fade-in ' . $classes]) }} role="alert">
    {{-- Icon --}}
    <div class="shrink-0 mt-0.5">
        @if($type === 'success')
            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @elseif($type === 'error')
            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @elseif($type === 'warning')
            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        @else
            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @endif
    </div>

    {{-- Content --}}
    <div class="flex-1">
        @if($title)
            <h3 class="text-sm font-bold">{{ $title }}</h3>
        @endif
        
        <div class="{{ $title ? 'mt-1 text-sm' : 'text-sm font-medium' }}">
            {{ $message ?? $slot }}
        </div>
    </div>

    {{-- Close Button --}}
    @if($dismissible)
        <button type="button" onclick="this.closest('[role=\'alert\']').remove()" class="shrink-0 ml-4 {{ $iconColor }} hover:text-black transition-colors" aria-label="Tutup alert">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
