<x-admin.layout title="Blog">
    <x-admin.page-header title="Blog" :description="auth()->user()->hasPermission(App\Enums\Permission::ManageAllPosts) ? 'Todos os artigos do blog.' : 'Os seus artigos.'">
        <x-admin.trash-toggle />
        <x-admin.button :href="route('admin.posts.create')" icon="plus">Novo artigo</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar por título…">
        <input type="hidden" name="trashed" value="{{ request('trashed') }}">
        <select name="status" class="admin-input sm:w-40" aria-label="Estado">
            <option value="">Todos os estados</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="category" class="admin-input sm:w-48" aria-label="Categoria">
            <option value="">Todas as categorias</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Artigo</th><th class="px-5 py-3">Autor</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3">Data</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="h-12 w-16 shrink-0 overflow-hidden rounded-lg bg-gray-100">
                                        @if ($post->coverImage)<img src="{{ $post->coverImage->thumbnailUrl() }}" alt="" class="size-full object-cover" loading="lazy">@endif
                                    </span>
                                    <div class="min-w-0">
                                        <p class="max-w-md truncate font-semibold text-gray-900">{{ $post->title }} @if ($post->is_featured)<x-lucide-star class="inline size-3.5 fill-ochre-500 text-ochre-500" />@endif</p>
                                        <p class="text-xs text-gray-500">{{ $post->category?->name ?? 'Sem categoria' }} · {{ $post->reading_time }} min</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $post->author?->name ?? '—' }}</td>
                            <td class="px-5 py-3"><x-admin.badge :color="$post->status->color()">{{ $post->status->label() }}</x-admin.badge></td>
                            <td class="px-5 py-3 whitespace-nowrap text-gray-600">{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @if ($post->trashed())
                                    <form method="POST" action="{{ route('admin.posts.restore', $post->id) }}" class="inline">@csrf<x-admin.button variant="secondary" size="sm" icon="rotate-ccw">Restaurar</x-admin.button></form>
                                @else
                                    <a href="{{ $post->isLive() ? $post->url() : route('admin.posts.preview', $post) }}" target="_blank" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="{{ $post->isLive() ? 'Ver no website' : 'Pré-visualizar' }}"><x-lucide-eye class="size-4" /></a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                    <x-admin.delete-button :action="route('admin.posts.destroy', $post)" icon-only confirm="Mover este artigo para a reciclagem?" />
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-admin.empty :colspan="5" icon="newspaper" title="Nenhum artigo encontrado" />
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
</x-admin.layout>
