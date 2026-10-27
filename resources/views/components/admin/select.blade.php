@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'help' => null, 'required' => false])

@php($key = str_replace(['[', ']'], ['.', ''], $name))

<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="admin-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->except('class')->class(['admin-input', 'border-red-400!' => $errors->has($key)]) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($key, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
