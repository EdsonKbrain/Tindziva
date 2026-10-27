{{-- Requires a parent x-data="lightbox(items)" scope --}}
<template x-teleport="body">
    <div x-cloak x-show="current" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center bg-brown-950/95 p-4"
         @keydown.escape.window="close()" @keydown.arrow-right.window="current && next()" @keydown.arrow-left.window="current && prev()"
         role="dialog" aria-modal="true" aria-label="Galeria de imagens">
        <button type="button" @click="close()" class="absolute top-4 right-4 grid size-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" aria-label="Fechar">
            <x-lucide-x class="size-6" />
        </button>
        <button type="button" x-show="items.length > 1" @click="prev()" class="absolute left-4 grid size-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" aria-label="Anterior">
            <x-lucide-chevron-left class="size-6" />
        </button>
        <figure class="max-h-full max-w-6xl" x-trap="current">
            <template x-if="current">
                <div>
                    <img :src="current.url" :alt="current.alt" class="max-h-[80vh] w-auto rounded-2xl object-contain">
                    <figcaption x-show="current.caption" x-text="current.caption" class="mt-4 text-center text-white/80"></figcaption>
                </div>
            </template>
        </figure>
        <button type="button" x-show="items.length > 1" @click="next()" class="absolute right-4 grid size-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white/20" aria-label="Seguinte">
            <x-lucide-chevron-right class="size-6" />
        </button>
    </div>
</template>
