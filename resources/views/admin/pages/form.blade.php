<x-admin.layout :title="$page->exists ? 'Editar página' : 'Nova página'">
    <x-admin.page-header :title="$page->exists ? $page->title : 'Nova página'" :description="$templates[$page->template] ?? null" :back="route('admin.pages.index')">
        @if ($page->exists && $page->is_published)
            <x-admin.button :href="$page->url()" target="_blank" variant="secondary" icon="external-link">Ver no website</x-admin.button>
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($page->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card>
                <div class="space-y-5" x-data="slugField(@js(old('title', $page->title)), @js(old('slug', $page->slug)))">
                    <x-admin.input name="title" label="Título" required :value="$page->title" x-model="title" help="Use *asteriscos* para destacar uma palavra no estilo editorial, por exemplo: Vamos *conversar*." />
                    @unless ($page->is_system)
                        <x-admin.input name="slug" label="Endereço (slug)" :value="$page->slug" x-model="slug" @input="touched = true" :help="'O endereço final será '.url('/').'/'.'…'" />
                    @endunless
                    <x-admin.textarea name="subtitle" label="Subtítulo" :value="$page->subtitle" :maxlength="500" rows="2" />
                    @if (! in_array($page->template, ['home', 'about', 'contact'], true))
                        <x-admin.editor name="content" label="Conteúdo" :value="$page->content" />
                    @endif
                </div>
            </x-admin.card>

            @foreach ($schema as $sectionKey => $section)
                <x-admin.card :title="$section['label']" :description="$section['description'] ?? null">
                    <div class="space-y-5">
                        @if ($section['toggle'] ?? true)
                            <x-admin.toggle :name="'sections['.$sectionKey.'][_visible]'" label="Mostrar esta secção no website"
                                :checked="old('sections.'.$sectionKey.'._visible', data_get($page->sections, $sectionKey.'._visible', true))"
                                class="rounded-lg bg-gray-50 p-3" />
                        @endif
                        @foreach ($section['fields'] as $fieldKey => $field)
                            @include('admin.pages.partials.field', [
                                'name' => "sections[{$sectionKey}][{$fieldKey}]",
                                'field' => $field,
                                'value' => old("sections.{$sectionKey}.{$fieldKey}", data_get($page->sections, "{$sectionKey}.{$fieldKey}")),
                            ])
                        @endforeach
                    </div>
                </x-admin.card>
            @endforeach

            <x-admin.seo-fields :model="$page" />
        </div>

        <div class="space-y-6">
            <x-admin.card title="Publicação">
                <div class="space-y-5">
                    @unless ($page->is_system)
                        <x-admin.toggle name="is_published" label="Publicada" :checked="$page->is_published" />
                        <x-admin.toggle name="show_in_menu" label="Mostrar no menu principal" :checked="$page->show_in_menu" />
                        <x-admin.input name="sort_order" type="number" min="0" label="Ordem no menu" :value="$page->sort_order ?? 0" />
                    @else
                        <p class="text-sm text-gray-600">Página do sistema: o endereço é fixo e a página está sempre publicada.</p>
                    @endunless
                    <x-admin.button class="w-full" icon="check">Guardar</x-admin.button>
                </div>
            </x-admin.card>
            <x-admin.card title="Imagem de destaque">
                <x-admin.media-field name="hero_image_id" :media="$page->heroImage" help="Apresentada no topo da página." />
            </x-admin.card>
        </div>
    </form>

    @if ($page->exists && ! $page->is_system)
        <div class="mt-6">
            <x-admin.delete-button :action="route('admin.pages.destroy', $page)" label="Mover para a reciclagem" confirm="Mover esta página para a reciclagem?" />
        </div>
    @endif
</x-admin.layout>
