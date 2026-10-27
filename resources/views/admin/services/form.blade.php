@use('App\Support\PageTemplates')

<x-admin.layout :title="$service->exists ? 'Editar serviço' : 'Novo serviço'">
    <x-admin.page-header :title="$service->exists ? $service->name : 'Novo serviço'" :back="route('admin.services.index')">
        @if ($service->exists && $service->is_published)
            <x-admin.button :href="$service->url()" target="_blank" variant="secondary" icon="external-link">Ver no website</x-admin.button>
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card>
                <div class="space-y-5" x-data="slugField(@js(old('name', $service->name)), @js(old('slug', $service->slug)))">
                    <x-admin.input name="name" label="Nome" required :value="$service->name" x-model="title" />
                    <x-admin.input name="slug" label="Endereço (slug)" :value="$service->slug" x-model="slug" @input="touched = true" help="Gerado automaticamente a partir do nome." />
                    <x-admin.textarea name="excerpt" label="Resumo" :value="$service->excerpt" :maxlength="500" rows="3" help="Apresentado nos cartões e no topo da página do serviço." />
                    <x-admin.editor name="description" label="Descrição" :value="$service->description" />
                </div>
            </x-admin.card>

            <x-admin.card title="Benefícios" description="Lista de vantagens apresentada na página do serviço.">
                <div x-data="{ items: @js(array_values(old('benefits', $service->benefits ?? []))) }" class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2">
                            <input type="text" name="benefits[]" x-model="items[index]" class="admin-input" maxlength="300" :aria-label="'Benefício ' + (index + 1)">
                            <button type="button" @click="items.splice(index, 1)" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Remover"><x-lucide-x class="size-4" /></button>
                        </div>
                    </template>
                    <button type="button" @click="items.push('')" class="inline-flex items-center gap-1 text-sm font-semibold text-ochre-700"><x-lucide-plus class="size-4" /> Adicionar benefício</button>
                </div>
            </x-admin.card>

            <x-admin.card title="Imagens">
                <div class="space-y-6">
                    <x-admin.media-field name="image_id" label="Imagem principal" :media="$service->image" />
                    <x-admin.gallery-field :items="$service->gallery ?? collect()" type="image" />
                </div>
            </x-admin.card>

            <x-admin.seo-fields :model="$service" />
        </div>

        <div class="space-y-6">
            <x-admin.card title="Publicação">
                <div class="space-y-5">
                    <x-admin.toggle name="is_published" label="Publicado" :checked="$service->is_published" help="Visível no website." />
                    <x-admin.toggle name="is_featured" label="Destaque" :checked="$service->is_featured" />
                    <x-admin.input name="sort_order" type="number" min="0" label="Ordem de apresentação" :value="$service->sort_order ?? 0" />
                    <x-admin.button class="w-full" icon="check">Guardar</x-admin.button>
                </div>
            </x-admin.card>
            <x-admin.card title="Organização">
                <div class="space-y-5">
                    <x-admin.select name="service_category_id" label="Categoria" :options="$categories->pluck('name', 'id')" :value="$service->service_category_id" placeholder="Sem categoria" />
                    <div x-data="{ icon: @js(old('icon', $service->icon)) }">
                        <span class="admin-label">Ícone</span>
                        <input type="hidden" name="icon" :value="icon">
                        <div class="grid grid-cols-6 gap-2">
                            @foreach (PageTemplates::icons() as $icon)
                                <button type="button" @click="icon = @js($icon)" class="grid aspect-square place-items-center rounded-lg border transition" :class="icon === @js($icon) ? 'border-ochre-600 bg-ochre-50 text-ochre-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'" title="{{ $icon }}">
                                    @svg('lucide-'.$icon, 'size-5')
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </form>

    @if ($service->exists)
        <div class="mt-6">
            <x-admin.delete-button :action="route('admin.services.destroy', $service)" label="Mover para a reciclagem" confirm="Mover este serviço para a reciclagem?" />
        </div>
    @endif
</x-admin.layout>
