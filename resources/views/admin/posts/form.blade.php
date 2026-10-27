@php
    $canPublish = auth()->user()->can('publish', [App\Models\Post::class, $post->exists ? $post : null]);
    $canChangeAuthor = auth()->user()->hasPermission(App\Enums\Permission::ManageAllPosts);
@endphp

<x-admin.layout :title="$post->exists ? 'Editar artigo' : 'Novo artigo'">
    <x-admin.page-header :title="$post->exists ? $post->title : 'Novo artigo'" :back="route('admin.posts.index')">
        @if ($post->exists)
            <x-admin.button :href="route('admin.posts.preview', $post)" target="_blank" variant="secondary" icon="eye">Pré-visualizar</x-admin.button>
            @if ($post->isLive())
                <x-admin.button :href="$post->url()" target="_blank" variant="secondary" icon="external-link">Ver no website</x-admin.button>
            @endif
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="grid gap-6 xl:grid-cols-3"
          x-data="{ status: @js(old('status', $post->status?->value ?? 'draft')) }">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card>
                <div class="space-y-5" x-data="slugField(@js(old('title', $post->title)), @js(old('slug', $post->slug)))">
                    <x-admin.input name="title" label="Título" required :value="$post->title" x-model="title" class="[&_input]:text-lg [&_input]:font-semibold" />
                    <x-admin.input name="slug" label="Endereço (slug)" :value="$post->slug" x-model="slug" @input="touched = true" />
                    <x-admin.textarea name="excerpt" label="Resumo" :value="$post->excerpt" :maxlength="500" rows="3" help="Apresentado na listagem do blog e nas partilhas." />
                    <x-admin.editor name="content" label="Conteúdo" :value="$post->content" placeholder="Comece a escrever o seu artigo…" />
                </div>
            </x-admin.card>

            <x-admin.seo-fields :model="$post" />
        </div>

        <div class="space-y-6">
            <x-admin.card title="Publicação">
                <div class="space-y-5">
                    <div>
                        <label for="status" class="admin-label">Estado</label>
                        <select id="status" name="status" x-model="status" class="admin-input">
                            @foreach ($statuses as $status)
                                @if ($canPublish || $status === App\Enums\PostStatus::Draft || $status === $post->status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endif
                            @endforeach
                        </select>
                        @unless ($canPublish)
                            <p class="mt-1 text-xs text-gray-500">Um editor irá rever e publicar o seu artigo.</p>
                        @endunless
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div x-show="status !== 'draft'" x-cloak>
                        <x-admin.input name="published_at" type="datetime-local" :value="$post->published_at?->format('Y-m-d\TH:i')"
                            x-bind:required="status === 'scheduled'"
                            label="Data de publicação" help="Os artigos agendados são publicados automaticamente nesta data." />
                    </div>
                    <x-admin.toggle name="is_featured" label="Artigo em destaque" :checked="$post->is_featured" />
                    <x-admin.button class="w-full" icon="check">
                        <span x-text="status === 'published' ? 'Publicar' : (status === 'scheduled' ? 'Agendar' : 'Guardar rascunho')">Guardar</span>
                    </x-admin.button>
                </div>
            </x-admin.card>

            <x-admin.card title="Organização">
                <div class="space-y-5">
                    <x-admin.select name="post_category_id" label="Categoria" :options="$categories->pluck('name', 'id')" :value="$post->post_category_id" placeholder="Sem categoria" />
                    <x-admin.input name="tags" label="Etiquetas" :value="$post->tags?->pluck('name')->join(', ')" placeholder="branding, eventos, imprensa" help="Separe as etiquetas por vírgulas." />
                    @if ($canChangeAuthor)
                        <x-admin.select name="user_id" label="Autor" :options="$authors->pluck('name', 'id')" :value="$post->user_id" />
                    @endif
                </div>
            </x-admin.card>

            <x-admin.card title="Imagem de capa">
                <x-admin.media-field name="cover_image_id" :media="$post->coverImage" help="Recomendado: 1600×900 px." />
            </x-admin.card>
        </div>
    </form>

    @if ($post->exists)
        <div class="mt-6">
            <x-admin.delete-button :action="route('admin.posts.destroy', $post)" label="Mover para a reciclagem" confirm="Mover este artigo para a reciclagem?" />
        </div>
    @endif
</x-admin.layout>
