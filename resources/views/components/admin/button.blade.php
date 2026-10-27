@props(['href' => null, 'variant' => 'primary', 'icon' => null, 'type' => 'submit', 'size' => 'md'])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ochre-600 disabled:opacity-50',
        'px-4 py-2 text-sm' => $size === 'md',
        'px-2.5 py-1.5 text-xs' => $size === 'sm',
        'bg-ochre-600 text-white hover:bg-ochre-700' => $variant === 'primary',
        'bg-brown-800 text-white hover:bg-brown-900' => $variant === 'dark',
        'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' => $variant === 'secondary',
        'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => $variant === 'ghost',
        'bg-red-600 text-white hover:bg-red-700' => $variant === 'danger',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon) @svg('lucide-'.$icon, 'size-4') @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon) @svg('lucide-'.$icon, 'size-4') @endif
        {{ $slot }}
    </button>
@endif
