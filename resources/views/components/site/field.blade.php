@props(['name', 'label', 'type' => 'text', 'required' => false, 'value' => null, 'bag' => 'default', 'options' => null, 'placeholder' => null, 'rows' => 5])

@php
    $id = $attributes->get('id', $bag.'-'.$name);
    $error = $errors->getBag($bag)->first($name);
    $current = $errors->getBag($bag)->any() ? old($name, $value) : $value;
@endphp

<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="form-label-site">
        {{ $label }} @if ($required)<span class="text-ochre-600" aria-hidden="true">*</span>@endif
    </label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @required($required) placeholder="{{ $placeholder }}"
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            class="form-input-site resize-y">{{ $current }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" @required($required) class="form-input-site" @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
            <option value="">{{ $placeholder ?? site_text('form_select') }}</option>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ $current }}" @required($required) placeholder="{{ $placeholder }}"
            {{ $attributes->except(['class', 'id']) }}
            @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            class="form-input-site">
    @endif
    @if ($error)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-700">{{ $error }}</p>
    @endif
</div>
