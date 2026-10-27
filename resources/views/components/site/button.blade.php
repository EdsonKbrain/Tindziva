@props(['href' => null, 'variant' => 'primary', 'size' => 'md', 'icon' => null, 'type' => 'submit'])

@php
    $classes = \Illuminate\Support\Arr::toCssClasses([
        'group/btn inline-flex items-center gap-2 rounded-full font-display font-semibold transition duration-300 ease-out-soft focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        'px-5 py-2.5 text-sm' => $size === 'sm',
        'px-7 py-3.5 text-[15px]' => $size === 'md',
        'px-8 py-4 text-base' => $size === 'lg',
        'bg-ochre-600 text-white shadow-lg shadow-ochre-600/25 hover:bg-ochre-700 hover:shadow-ochre-700/30' => $variant === 'primary',
        'bg-brown-800 text-white hover:bg-brown-900' => $variant === 'dark',
        'border border-brown-200 bg-white text-brown-800 hover:border-brown-800' => $variant === 'outline',
        'bg-white text-brown-800 hover:bg-ochre-50' => $variant === 'light',
        'border border-white/30 text-white hover:bg-white/10' => $variant === 'ghost-light',
        'px-0! py-0! text-ochre-700 shadow-none! hover:text-ochre-800' => $variant === 'link',
    ]);
    $iconName = $icon ?? ($variant === 'link' || $variant === 'primary' ? 'arrow-right' : null);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <span>{{ $slot }}</span>
        @if ($iconName)
            @svg('lucide-'.$iconName, 'size-4 transition-transform duration-300 group-hover/btn:translate-x-0.5')
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        <span>{{ $slot }}</span>
        @if ($iconName)
            @svg('lucide-'.$iconName, 'size-4 transition-transform duration-300 group-hover/btn:translate-x-0.5')
        @endif
    </button>
@endif
