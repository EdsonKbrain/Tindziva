<x-admin.layout title="Categorias">
    <x-admin.page-header title="Categorias e etiquetas" description="Organize serviços, portfólio e blog." />

    <div class="mb-6 flex gap-1 overflow-x-auto rounded-lg bg-gray-200/60 p-1 text-sm">
        @foreach ($types as $key => $typeConfig)
            <a href="{{ route('admin.categories.index', $key) }}" @class(['rounded-md px-4 py-2 font-medium whitespace-nowrap', 'bg-white shadow-xs' => $type === $key, 'text-gray-600 hover:text-gray-900' => $type !== $key])>{{ $typeConfig['label'] }}</a>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.filters placeholder="Pesquisar…" />
            <div class="admin-card overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                        <tr><th class="px-5 py-3">Nome</th><th class="px-5 py-3">Slug</th><th class="px-5 py-3">Utilizações</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($items as $item)
                            <tr @class(['hover:bg-gray-50', 'bg-ochre-50/60' => $editing?->is($item)])>
                                <td class="px-5 py-3 font-semibold text-gray-900">{{ $item->name }}</td>
                                <td class="px-5 py-3 text-gray-500">{{ $item->slug }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $item->{$config['count'].'_count'} }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.categories.index', [$type, 'edit' => $item->id]) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                    <x-admin.delete-button :action="route('admin.categories.destroy', [$type, $item->id])" icon-only confirm="Eliminar? Os conteúdos associados ficarão sem esta classificação." />
                                </td>
                            </tr>
                        @empty
                            <x-admin.empty :colspan="4" icon="tags" title="Sem registos" />
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>

        <x-admin.card :title="$editing ? 'Editar '.$config['singular'] : 'Nova '.$config['singular']">
            <form method="POST" action="{{ $editing ? route('admin.categories.update', [$type, $editing->id]) : route('admin.categories.store', $type) }}" class="space-y-4">
                @csrf
                @if ($editing) @method('PUT') @endif
                <x-admin.input name="name" label="Nome" :value="$editing?->name" required />
                <x-admin.input name="slug" label="Slug" :value="$editing?->slug" help="Opcional — gerado a partir do nome." />
                @if ($type !== 'tags')
                    <x-admin.textarea name="description" label="Descrição" :value="$editing?->description" rows="3" />
                    <x-admin.input name="sort_order" type="number" min="0" label="Ordem" :value="$editing?->sort_order ?? 0" />
                @endif
                <div class="flex gap-2">
                    <x-admin.button icon="check">{{ $editing ? 'Actualizar' : 'Criar' }}</x-admin.button>
                    @if ($editing)
                        <x-admin.button :href="route('admin.categories.index', $type)" variant="ghost">Cancelar</x-admin.button>
                    @endif
                </div>
            </form>
        </x-admin.card>
    </div>
</x-admin.layout>
