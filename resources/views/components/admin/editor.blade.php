@props(['name', 'label' => null, 'value' => null, 'placeholder' => null, 'help' => null])

@php($key = str_replace(['[', ']'], ['.', ''], $name))

<div {{ $attributes->class('') }}>
    @if ($label)
        <span class="admin-label">{{ $label }}</span>
    @endif
    <div x-data="richEditor" data-placeholder="{{ $placeholder }}">
        <input type="hidden" name="{{ $name }}" x-ref="input" value="{{ old($key, $value) }}">
        <div x-ref="editor" class="bg-white"></div>
    </div>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
