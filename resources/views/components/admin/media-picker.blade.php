<div x-data x-cloak x-show="$store.picker.isOpen" class="fixed inset-0 z-[80] flex items-end justify-center sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="picker-title"
     @keydown.escape.window="$store.picker.isOpen && $store.picker.close()">
    <div class="absolute inset-0 bg-gray-900/60" @click="$store.picker.close()"></div>
    <div class="relative flex max-h-[90vh] w-full max-w-5xl flex-col rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" x-trap.noscroll="$store.picker.isOpen">
        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4">
            <h2 id="picker-title" class="font-display text-lg font-semibold text-gray-900">Biblioteca multimédia</h2>
            <button type="button" @click="$store.picker.close()" class="text-gray-400 hover:text-gray-600" aria-label="Fechar"><x-lucide-x class="size-5" /></button>
        </div>

        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-gray-400" />
                <input type="search" x-model.debounce.400ms="$store.picker.query" @input.debounce.400ms="$store.picker.load(true)" placeholder="Pesquisar ficheiros…" class="admin-input pl-9">
            </div>
            <label class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-brown-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brown-900">
                <x-lucide-upload class="size-4" />
                <span x-text="$store.picker.uploading ? 'A carregar…' : 'Carregar ficheiros'"></span>
                <input type="file" multiple class="sr-only" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,application/pdf" @change="$store.picker.upload($event.target.files); $event.target.value = ''">
            </label>
        </div>

        <p x-show="$store.picker.error" x-text="$store.picker.error" class="mx-5 mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></p>

        <div class="flex-1 overflow-y-auto p-5"
             @dragover.prevent @drop.prevent="$store.picker.upload($event.dataTransfer.files)">
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                <template x-for="item in $store.picker.items" :key="item.id">
                    <button type="button" @click="$store.picker.toggle(item)" @dblclick="$store.picker.toggle(item); $store.picker.confirm()"
                            class="group relative aspect-square overflow-hidden rounded-lg border-2 bg-gray-100 text-left"
                            :class="$store.picker.isSelected(item) ? 'border-ochre-600 ring-2 ring-ochre-600/30' : 'border-transparent hover:border-gray-300'"
                            :aria-pressed="$store.picker.isSelected(item)" :title="item.title">
                        <template x-if="item.type === 'image'"><img :src="item.thumbnail" :alt="item.alt" class="size-full object-cover" loading="lazy"></template>
                        <template x-if="item.type !== 'image'">
                            <span class="flex size-full flex-col items-center justify-center gap-2 p-2 text-center text-xs text-gray-500">
                                <x-lucide-file class="size-8" /><span class="line-clamp-2" x-text="item.title"></span>
                            </span>
                        </template>
                        <span x-show="$store.picker.isSelected(item)" class="absolute top-1.5 right-1.5 grid size-6 place-items-center rounded-full bg-ochre-600 text-white"><x-lucide-check class="size-4" /></span>
                    </button>
                </template>
            </div>
            <p x-show="!$store.picker.loading && $store.picker.items.length === 0" class="py-16 text-center text-sm text-gray-500">
                Sem ficheiros. Arraste imagens para aqui ou utilize «Carregar ficheiros».
            </p>
            <div class="mt-5 text-center" x-show="$store.picker.nextUrl">
                <button type="button" @click="$store.picker.load()" class="text-sm font-semibold text-ochre-700" :disabled="$store.picker.loading">Carregar mais</button>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-gray-200 px-5 py-4">
            <p class="text-sm text-gray-500" x-text="$store.picker.selected.length ? `${$store.picker.selected.length} seleccionado(s)` : 'Seleccione um ficheiro'"></p>
            <div class="flex gap-2">
                <button type="button" @click="$store.picker.close()" class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Cancelar</button>
                <button type="button" @click="$store.picker.confirm()" :disabled="!$store.picker.selected.length" class="rounded-lg bg-ochre-600 px-4 py-2 text-sm font-semibold text-white hover:bg-ochre-700 disabled:opacity-50">Utilizar</button>
            </div>
        </div>
    </div>
</div>
