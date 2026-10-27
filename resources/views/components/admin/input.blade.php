@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'help' => null, 'required' => false, 'errorKey' => null])

@php
    $id = $attributes->get('id', str_replace(['[', ']', '.'], ['-', '', '-'], $name));
    $key = $errorKey ?? str_replace(['[', ']'], ['.', ''], $name);
@endphp

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="admin-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($key, $value) }}" @required($required)
        {{ $attributes->except(['class', 'id'])->class(['admin-input', 'border-red-400!' => $errors->has($key)]) }}>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
