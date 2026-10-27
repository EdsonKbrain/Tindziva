<x-admin.layout title="Multimédia">
    <x-admin.page-header title="Biblioteca multimédia" description="Imagens (JPG, PNG, WebP, GIF até 10 MB), vídeos (MP4, WebM até 60 MB) e PDF (até 20 MB). As imagens são optimizadas automaticamente." />

    <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="admin-card mb-6 p-5"
          x-data="{ count: 0, over: false }" @dragover.prevent="over = true" @dragleave.prevent="over = false"
          @drop.prevent="over = false; $refs.files.files = $event.dataTransfer.files; count = $refs.files.files.length">
        @csrf
        <label class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-6 py-8 text-center transition" :class="over ? 'border-ochre-600 bg-ochre-50' : 'border-gray-300 hover:border-gray-400'">
            <x-lucide-upload class="size-8 text-gray-400" />
            <span class="text-sm font-semibold text-gray-800">Arraste ficheiros para aqui ou clique para seleccionar</span>
            <span class="text-xs text-gray-500" x-text="count ? count + ' ficheiro(s) seleccionado(s)' : 'Até 20 ficheiros de cada vez'"></span>
            <input x-ref="files" type="file" name="files[]" multiple class="sr-only" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,application/pdf" @change="count = $event.target.files.length">
        </label>
        <div class="mt-4 grid gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <x-admin.input name="alt_text" label="Texto alternativo (opcional)" help="Descreva a imagem para acessibilidade e SEO." />
            <x-admin.input name="caption" label="Legenda (opcional)" />
            <x-admin.button icon="upload" x-bind:disabled="!count">Carregar</x-admin.button>
        </div>
        @if ($errors->any())
            <ul class="mt-3 space-y-1 text-sm text-red-600">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        @endif
    </form>

    <x-admin.filters placeholder="Pesquisar por nome, legenda ou texto alternativo…">
        <select name="type" class="admin-input sm:w-40" aria-label="Tipo">
            <option value="">Todos os tipos</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-gray-700 whitespace-nowrap"><input type="checkbox" name="mine" value="1" @checked(request()->boolean('mine')) class="rounded border-gray-300 text-ochre-600"> Os meus</label>
    </x-admin.filters>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6">
        @forelse ($media as $item)
            <div class="admin-card group overflow-hidden" x-data="{ editing: false }">
                <div class="relative aspect-square bg-gray-100">
                    @if ($item->isImage())
                        <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->altText() }}" loading="lazy" class="size-full object-cover">
                    @else
                        <div class="flex size-full flex-col items-center justify-center gap-2 text-gray-400">@svg('lucide-'.($item->isVideo() ? 'video' : 'file-text'), 'size-10')<span class="text-xs uppercase">{{ pathinfo($item->path, PATHINFO_EXTENSION) }}</span></div>
                    @endif
                    @if (! $item->alt_text && $item->isImage())
                        <span class="absolute top-2 left-2 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800" title="Sem texto alternativo">Sem alt</span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="truncate text-sm font-semibold text-gray-900" title="{{ $item->title }}">{{ $item->title }}</p>
                    <p class="text-xs text-gray-500">{{ $item->humanSize() }}@if ($item->width) · {{ $item->width }}×{{ $item->height }}@endif</p>
                    <div class="mt-2 flex items-center justify-between">
                        <button type="button" @click="navigator.clipboard?.writeText(@js($item->url()))" class="text-xs font-medium text-gray-500 hover:text-gray-800" title="Copiar URL"><x-lucide-copy class="inline size-3.5" /> URL</button>
                        @can('update', $item)
                            <div class="flex">
                                <button type="button" @click="editing = true" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700" aria-label="Editar"><x-lucide-pencil class="size-4" /></button>
                                <x-admin.delete-button :action="route('admin.media.destroy', $item)" icon-only confirm="Eliminar este ficheiro? Será removido de todos os conteúdos que o utilizam." />
                            </div>
                        @endcan
                    </div>
                </div>
                @can('update', $item)
                    <div x-cloak x-show="editing" class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/50 p-4" @keydown.escape.window="editing = false">
                        <form method="POST" action="{{ route('admin.media.update', $item) }}" class="w-full max-w-md space-y-4 rounded-2xl bg-white p-6 shadow-xl" @click.outside="editing = false">
                            @csrf @method('PUT')
                            <h2 class="font-display text-lg font-semibold">Editar ficheiro</h2>
                            <x-admin.input name="title" label="Título" :value="$item->title" :id="'title-'.$item->id" />
                            <x-admin.input name="alt_text" label="Texto alternativo" :value="$item->alt_text" :id="'alt-'.$item->id" />
                            <x-admin.input name="caption" label="Legenda" :value="$item->caption" :id="'caption-'.$item->id" />
                            <p class="text-xs text-gray-500">Carregado por {{ $item->uploader?->name ?? '—' }} em {{ $item->created_at->format('d/m/Y') }}</p>
                            <div class="flex justify-end gap-2">
                                <x-admin.button type="button" variant="ghost" @click="editing = false">Cancelar</x-admin.button>
                                <x-admin.button icon="check">Guardar</x-admin.button>
                            </div>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="admin-card col-span-full"><x-admin.empty icon="image" title="A biblioteca está vazia">Carregue as primeiras imagens acima.</x-admin.empty></div>
        @endforelse
    </div>
    <div class="mt-6">{{ $media->links() }}</div>
</x-admin.layout>
