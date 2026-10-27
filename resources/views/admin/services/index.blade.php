<x-admin.layout title="Serviços">
    <x-admin.page-header title="Serviços" description="Serviços apresentados no website, pela ordem definida.">
        <x-admin.trash-toggle />
        <x-admin.button :href="route('admin.services.create')" icon="plus">Novo serviço</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar serviços…">
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
            <option value="draft" @selected(request('status') === 'draft')>Ocultos</option>
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Serviço</th><th class="px-5 py-3">Categoria</th><th class="px-5 py-3">Ordem</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($services as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-ochre-50 text-ochre-600">@svg('lucide-'.($service->icon ?: 'sparkles'), 'size-5')</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $service->name }}</p>
                                        <p class="text-xs text-gray-500">/servicos/{{ $service->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $service->category?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $service->sort_order }}</td>
                            <td class="px-5 py-3">
                                <x-admin.badge :color="$service->is_published ? 'green' : 'gray'">{{ $service->is_published ? 'Publicado' : 'Oculto' }}</x-admin.badge>
                                @if ($service->is_featured)<x-admin.badge color="orange">Destaque</x-admin.badge>@endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if ($service->trashed())
                                    <form method="POST" action="{{ route('admin.services.restore', $service->id) }}" class="inline">@csrf<x-admin.button variant="secondary" size="sm" icon="rotate-ccw">Restaurar</x-admin.button></form>
                                @else
                                    @if ($service->is_published)
                                        <a href="{{ $service->url() }}" target="_blank" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Ver no website"><x-lucide-external-link class="size-4" /></a>
                                    @endif
                                    <a href="{{ route('admin.services.edit', $service) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                    <x-admin.delete-button :action="route('admin.services.destroy', $service)" icon-only confirm="Mover este serviço para a reciclagem?" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty :colspan="5" icon="briefcase" title="Nenhum serviço encontrado" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
</x-admin.layout>
