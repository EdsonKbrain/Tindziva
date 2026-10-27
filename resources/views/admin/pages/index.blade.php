<x-admin.layout title="Páginas">
    <x-admin.page-header title="Páginas" description="Páginas institucionais do website e páginas personalizadas.">
        <x-admin.trash-toggle />
        <x-admin.button :href="route('admin.pages.create')" icon="plus">Nova página</x-admin.button>
    </x-admin.page-header>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Página</th><th class="px-5 py-3">Tipo</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3">Actualizada</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-gray-900">{{ $page->title }}</p>
                                <p class="text-xs text-gray-500">{{ parse_url($page->url(), PHP_URL_PATH) ?: '/' }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $templates[$page->template] ?? $page->template }}
                                @if ($page->is_system)<x-admin.badge color="blue">Sistema</x-admin.badge>@endif
                            </td>
                            <td class="px-5 py-3">
                                <x-admin.badge :color="$page->is_published ? 'green' : 'gray'">{{ $page->is_published ? 'Publicada' : 'Rascunho' }}</x-admin.badge>
                                @if ($page->show_in_menu)<x-admin.badge color="orange">No menu</x-admin.badge>@endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $page->updated_at->diffForHumans() }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if ($page->trashed())
                                    <form method="POST" action="{{ route('admin.pages.restore', $page->id) }}" class="inline">@csrf<x-admin.button variant="secondary" size="sm" icon="rotate-ccw">Restaurar</x-admin.button></form>
                                @else
                                    @if ($page->is_published)
                                        <a href="{{ $page->url() }}" target="_blank" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Ver no website"><x-lucide-external-link class="size-4" /></a>
                                    @endif
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                    @can('delete', $page)
                                        <x-admin.delete-button :action="route('admin.pages.destroy', $page)" icon-only confirm="Mover esta página para a reciclagem?" />
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty :colspan="5" icon="file-text" title="Sem páginas" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $pages->links() }}</div>
</x-admin.layout>
