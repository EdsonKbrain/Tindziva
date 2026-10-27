<x-admin.layout title="Portfólio">
    <x-admin.page-header title="Portfólio" description="Projectos apresentados no website.">
        <x-admin.trash-toggle />
        <x-admin.button :href="route('admin.projects.create')" icon="plus">Novo projecto</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar projectos…">
        <input type="hidden" name="trashed" value="{{ request('trashed') }}">
        <select name="category" class="admin-input sm:w-48" aria-label="Categoria">
            <option value="">Todas as categorias</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="admin-input sm:w-40" aria-label="Estado">
            <option value="">Todos os estados</option>
            <option value="published" @selected(request('status') === 'published')>Publicados</option>
            <option value="draft" @selected(request('status') === 'draft')>Rascunhos</option>
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Projecto</th><th class="px-5 py-3">Cliente</th><th class="px-5 py-3">Categorias</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="size-12 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        @if ($project->coverImage)<img src="{{ $project->coverImage->thumbnailUrl() }}" alt="" class="size-full object-cover" loading="lazy">@endif
                                    </span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $project->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $project->completed_at?->format('Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $project->client?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $project->categories->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="px-5 py-3">
                                <x-admin.badge :color="$project->is_published ? 'green' : 'gray'">{{ $project->is_published ? 'Publicado' : 'Rascunho' }}</x-admin.badge>
                                @if ($project->is_featured)<x-admin.badge color="orange">Destaque</x-admin.badge>@endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if ($project->trashed())
                                    <form method="POST" action="{{ route('admin.projects.restore', $project->id) }}" class="inline">@csrf<x-admin.button variant="secondary" size="sm" icon="rotate-ccw">Restaurar</x-admin.button></form>
                                @else
                                    @if ($project->is_published)
                                        <a href="{{ $project->url() }}" target="_blank" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Ver no website"><x-lucide-external-link class="size-4" /></a>
                                    @endif
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                    <x-admin.delete-button :action="route('admin.projects.destroy', $project)" icon-only confirm="Mover este projecto para a reciclagem?" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty :colspan="5" icon="images" title="Nenhum projecto encontrado" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $projects->links() }}</div>
</x-admin.layout>
