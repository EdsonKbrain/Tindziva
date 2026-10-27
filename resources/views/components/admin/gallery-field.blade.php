@props(['name' => 'gallery', 'label' => 'Galeria', 'items' => collect(), 'type' => '', 'help' => 'Arraste para reordenar.'])

<div {{ $attributes }} x-data="galleryField(@js($items->map->toPickerArray()->values()), @js($type))">
    <div class="flex items-center justify-between">
        <span class="admin-label">{{ $label }}</span>
        <button type="button" @click="add()" class="inline-flex items-center gap-1 text-sm font-semibold text-ochre-700 hover:text-ochre-800"><x-lucide-plus class="size-4" /> Adicionar</button>
    </div>
    <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
        <template x-for="(item, index) in items" :key="item.id">
            <div class="group relative aspect-square cursor-move overflow-hidden rounded-lg border border-gray-200 bg-gray-100"
                 draggable="true" @dragstart="start(index)" @dragover.prevent="over(index)" @dragend="dragging = null"
                 :class="dragging === index && 'opacity-40'">
                <input type="hidden" name="{{ $name }}[]" :value="item.id">
                <template x-if="item.type === 'image'"><img :src="item.thumbnail" :alt="item.alt" class="size-full object-cover"></template>
                <template x-if="item.type !== 'image'"><span class="flex size-full flex-col items-center justify-center gap-1 p-2 text-center text-xs text-gray-500"><x-lucide-video class="size-6" /><span class="line-clamp-2" x-text="item.title"></span></span></template>
                <button type="button" @click="remove(index)" class="absolute top-1 right-1 grid size-7 place-items-center rounded-full bg-white/90 text-gray-700 opacity-0 shadow transition group-hover:opacity-100 focus:opacity-100 hover:text-red-600" aria-label="Remover">
                    <x-lucide-x class="size-4" />
                </button>
            </div>
        </template>
        <button type="button" @click="add()" class="grid aspect-square place-items-center rounded-lg border-2 border-dashed border-gray-300 text-gray-400 transition hover:border-ochre-600 hover:text-ochre-600" aria-label="Adicionar à galeria">
            <x-lucide-plus class="size-6" />
        </button>
    </div>
    @if ($help)
        <p class="mt-2 text-xs text-gray-500">{{ $help }}</p>
    @endif
</div>
