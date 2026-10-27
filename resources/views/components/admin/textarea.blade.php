@props(['name', 'label' => null, 'value' => null, 'help' => null, 'rows' => 3, 'required' => false, 'maxlength' => null])

@php($key = str_replace(['[', ']'], ['.', ''], $name))

<div {{ $attributes->only('class') }} @if ($maxlength) x-data="charCounter(@js(old($key, $value)), {{ $maxlength }})" @endif>
    @if ($label)
        <div class="flex items-center justify-between">
            <label for="{{ $name }}" class="admin-label">{{ $label }} @if ($required)<span class="text-red-600">*</span>@endif</label>
            @if ($maxlength)
                <span class="text-xs" :class="remaining < 0 ? 'text-red-600' : 'text-gray-400'" x-text="remaining"></span>
            @endif
        </div>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required) @if ($maxlength) x-model="value" @endif
        {{ $attributes->except('class')->class(['admin-input', 'border-red-400!' => $errors->has($key)]) }}>{{ old($key, $value) }}</textarea>
    @if ($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($key)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
