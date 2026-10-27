@props(['name', 'label', 'checked' => false, 'help' => null])

<label {{ $attributes->class('flex cursor-pointer items-start gap-3') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked)) class="peer sr-only">
    <span class="relative mt-0.5 inline-flex h-6 w-11 shrink-0 rounded-full bg-gray-200 transition peer-checked:bg-ochre-600 peer-focus-visible:ring-2 peer-focus-visible:ring-ochre-600 peer-focus-visible:ring-offset-2 after:absolute after:top-0.5 after:left-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
    <span>
        <span class="block text-sm font-medium text-gray-800">{{ $label }}</span>
        @if ($help)
            <span class="block text-xs text-gray-500">{{ $help }}</span>
        @endif
    </span>
</label>
