@props(['label', 'value', 'icon', 'href' => null, 'hint' => null])

@php($classes = \Illuminate\Support\Arr::toCssClasses(['admin-card flex items-center gap-4 p-5', 'transition hover:border-ochre-300 hover:shadow-md' => $href]))

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
@else
    <div {{ $attributes->class($classes) }}>
@endif
    <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-ochre-50 text-ochre-600">@svg('lucide-'.$icon, 'size-6')</span>
    <div>
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="font-display text-2xl font-semibold text-gray-900">{{ $value }}</p>
        @if ($hint)
            <p class="text-xs text-gray-500">{{ $hint }}</p>
        @endif
    </div>
@if ($href)
    </a>
@else
    </div>
@endif
