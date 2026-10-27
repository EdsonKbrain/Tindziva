<x-admin.layout :title="$project->exists ? 'Editar projecto' : 'Novo projecto'">
    <x-admin.page-header :title="$project->exists ? $project->title : 'Novo projecto'" :back="route('admin.projects.index')">
        @if ($project->exists && $project->is_published)
            <x-admin.button :href="$project->url()" target="_blank" variant="secondary" icon="external-link">Ver no website</x-admin.button>
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($project->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card>
                <div class="space-y-5" x-data="slugField(@js(old('title', $project->title)), @js(old('slug', $project->slug)))">
                    <x-admin.input name="title" label="Título" required :value="$project->title" x-model="title" />
                    <x-admin.input name="slug" label="Endereço (slug)" :value="$project->slug" x-model="slug" @input="touched = true" />
                    <x-admin.textarea name="excerpt" label="Resumo" :value="$project->excerpt" :maxlength="500" rows="3" />
                    <x-admin.editor name="description" label="Descrição do projecto" :value="$project->description" placeholder="Desafio, abordagem e resultados…" />
                </div>
            </x-admin.card>

            <x-admin.card title="Multimédia">
                <div class="space-y-6">
                    <x-admin.media-field name="cover_image_id" label="Imagem de capa" :media="$project->coverImage" help="Recomendado: 1600×1000 px ou superior." />
                    <x-admin.gallery-field :items="$project->gallery ?? collect()" label="Galeria de imagens e vídeos" />
                    <div x-data="{ urls: @js(array_values(old('video_urls', $project->video_urls ?? []))) }">
                        <span class="admin-label">Vídeos do YouTube ou Vimeo</span>
                        <div class="space-y-2">
                            <template x-for="(url, index) in urls" :key="index">
                                <div class="flex gap-2">
                                    <input type="url" name="video_urls[]" x-model="urls[index]" class="admin-input" placeholder="https://www.youtube.com/watch?v=…">
                                    <button type="button" @click="urls.splice(index, 1)" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Remover"><x-lucide-x class="size-4" /></button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="urls.push('')" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-ochre-700"><x-lucide-plus class="size-4" /> Adicionar vídeo</button>
                        @foreach ($errors->get('video_urls.*') as $messages)
                            <p class="mt-1 text-sm text-red-600">{{ $messages[0] }}</p>
                        @endforeach
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Cliente, créditos e autorizações" description="Só é apresentado no website o que o cliente autorizou.">
                <div class="space-y-5">
                    <x-admin.select name="client_id" label="Cliente" :options="$clients->pluck('name', 'id')" :value="$project->client_id" placeholder="Sem cliente associado" />
                    <x-admin.toggle name="show_client" label="Mostrar o cliente no website" :checked="$project->show_client" />
                    <div x-data="{ credits: @js(array_values(old('credits', $project->credits ?? []))) }">
                        <span class="admin-label">Créditos</span>
                        <div class="space-y-2">
                            <template x-for="(credit, index) in credits" :key="index">
                                <div class="grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
                                    <input type="text" :name="`credits[${index}][role]`" x-model="credit.role" class="admin-input" placeholder="Função (ex.: Fotografia)">
                                    <input type="text" :name="`credits[${index}][name]`" x-model="credit.name" class="admin-input" placeholder="Nome">
                                    <button type="button" @click="credits.splice(index, 1)" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" aria-label="Remover"><x-lucide-x class="size-4" /></button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="credits.push({ role: '', name: '' })" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-ochre-700"><x-lucide-plus class="size-4" /> Adicionar crédito</button>
                    </div>
                    <x-admin.toggle name="show_credits" label="Mostrar créditos no website" :checked="$project->show_credits" />
                    <x-admin.textarea name="usage_permission" label="Permissões de utilização" :value="$project->usage_permission" rows="2" help="Registo interno das autorizações de utilização de imagens e marca (não é publicado)." />
                </div>
            </x-admin.card>

            <x-admin.seo-fields :model="$project" />
        </div>

        <div class="space-y-6">
            <x-admin.card title="Publicação">
                <div class="space-y-5">
                    <x-admin.toggle name="is_published" label="Publicado" :checked="$project->is_published" />
                    <x-admin.toggle name="is_featured" label="Destaque na página inicial" :checked="$project->is_featured" />
                    <x-admin.input name="published_at" type="datetime-local" label="Data de publicação" :value="$project->published_at?->format('Y-m-d\TH:i')" help="Opcional. Uma data futura adia a publicação." />
                    <x-admin.input name="sort_order" type="number" min="0" label="Ordem" :value="$project->sort_order ?? 0" help="Menor número aparece primeiro." />
                    <x-admin.button class="w-full" icon="check">Guardar</x-admin.button>
                </div>
            </x-admin.card>
            <x-admin.card title="Detalhes">
                <div class="space-y-5">
                    <x-admin.input name="completed_at" type="date" label="Data de conclusão" :value="$project->completed_at?->format('Y-m-d')" />
                    <x-admin.input name="location" label="Local" :value="$project->location" placeholder="Maputo, Moçambique" />
                    <fieldset>
                        <legend class="admin-label">Categorias</legend>
                        <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3">
                            @php($selectedCategories = old('categories', $project->categories?->modelKeys() ?? []))
                            @forelse ($categories as $category)
                                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategories)) class="rounded border-gray-300 text-ochre-600 focus:ring-ochre-600"> {{ $category->name }}</label>
                            @empty
                                <p class="text-sm text-gray-500">Crie categorias em <a href="{{ route('admin.categories.index', 'projects') }}" class="text-ochre-700 underline">Categorias</a>.</p>
                            @endforelse
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend class="admin-label">Serviços relacionados</legend>
                        <div class="max-h-48 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-3">
                            @php($selectedServices = old('services', $project->services?->modelKeys() ?? []))
                            @foreach ($services as $service)
                                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="services[]" value="{{ $service->id }}" @checked(in_array($service->id, $selectedServices)) class="rounded border-gray-300 text-ochre-600 focus:ring-ochre-600"> {{ $service->name }}</label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </x-admin.card>
        </div>
    </form>

    @if ($project->exists)
        <div class="mt-6">
            <x-admin.delete-button :action="route('admin.projects.destroy', $project)" label="Mover para a reciclagem" confirm="Mover este projecto para a reciclagem?" />
        </div>
    @endif
</x-admin.layout>
