@props(['name', 'label' => null, 'media' => null, 'type' => 'image', 'help' => null])

<div {{ $attributes->class('') }} x-data="mediaField(@js($media?->toPickerArray()), @js($type))">
    @if ($label)
        <span class="admin-label">{{ $label }}</span>
    @endif
    <input type="hidden" name="{{ $name }}" :value="media ? media.id : ''">
    <div class="flex items-center gap-4">
        <button type="button" @click="pick()" class="relative grid size-24 shrink-0 place-items-center overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 text-gray-400 transition hover:border-ochre-600 hover:text-ochre-600" aria-label="Seleccionar ficheiro">
            <template x-if="media && media.type === 'image'"><img :src="media.thumbnail" :alt="media.alt" class="absolute inset-0 size-full object-cover"></template>
            <template x-if="!media"><x-lucide-image class="size-7" /></template>
            <template x-if="media && media.type !== 'image'"><x-lucide-file class="size-7" /></template>
        </button>
        <div class="min-w-0 text-sm">
            <p class="truncate font-medium text-gray-800" x-text="media ? media.title : 'Nenhum ficheiro seleccionado'"></p>
            <p class="text-xs text-gray-500" x-show="media" x-text="media ? [media.dimensions, media.size].filter(Boolean).join(' · ') : ''"></p>
            <div class="mt-2 flex gap-3">
                <button type="button" @click="pick()" class="font-semibold text-ochre-700 hover:text-ochre-800" x-text="media ? 'Alterar' : 'Seleccionar'"></button>
                <button type="button" x-show="media" @click="clear()" class="text-gray-500 hover:text-red-600">Remover</button>
            </div>
        </div>
    </div>
    @if ($help)
        <p class="mt-2 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
