<x-admin.layout title="Clientes e parceiros">
    <x-admin.page-header title="Clientes e parceiros" description="Logótipos apresentados no website. Arraste para alterar a ordem.">
        <x-admin.button :href="route('admin.clients.create')" icon="plus">Novo cliente</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar clientes…">
        <select name="type" class="admin-input sm:w-40" aria-label="Tipo">
            <option value="">Todos</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        <select name="segment" class="admin-input sm:w-56" aria-label="Segmento">
            <option value="">Todos os segmentos</option>
            @foreach ($segments as $segment)
                <option value="{{ $segment->value }}" @selected(request('segment') === $segment->value)>{{ $segment->label() }}</option>
            @endforeach
        </select>
    </x-admin.filters>

    <form method="POST" action="{{ route('admin.clients.reorder') }}" x-data="sortableList" class="admin-card overflow-hidden">
        @csrf
        <ul class="divide-y divide-gray-100" @dragover.prevent="over($event)">
            @forelse ($clients as $client)
                <li data-sortable-item draggable="true" @dragstart="start($event)" @dragend="end()" class="flex items-center gap-4 bg-white px-5 py-3 hover:bg-gray-50">
                    <input type="hidden" name="order[]" value="{{ $client->id }}">
                    <x-lucide-grip-vertical class="size-4 shrink-0 cursor-move text-gray-300" />
                    <span class="grid h-12 w-20 shrink-0 place-items-center overflow-hidden rounded-lg border border-gray-100 bg-white p-1">
                        @if ($client->logo)<img src="{{ $client->logo->thumbnailUrl() }}" alt="" class="max-h-full max-w-full object-contain">@else<x-lucide-building-2 class="size-5 text-gray-300" />@endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900">{{ $client->name }}</p>
                         <p class="text-xs text-gray-500">{{ $client->segment?->label() ?? $client->type->label() }} · {{ trans_choice('{0} sem projectos|{1} 1 projecto|[2,*] :count projectos', $client->projects_count) }}</p>
                    </div>
                    <x-admin.badge :color="$client->is_visible ? 'green' : 'gray'">{{ $client->is_visible ? 'Visível' : 'Oculto' }}</x-admin.badge>
                    <a href="{{ route('admin.clients.edit', $client) }}" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                </li>
            @empty
                <li><x-admin.empty icon="handshake" title="Sem clientes registados" /></li>
            @endforelse
        </ul>
        @if ($clients->count() > 1)
            <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-5 py-3">
                <x-admin.button variant="dark" icon="check">Guardar ordem</x-admin.button>
            </div>
        @endif
    </form>
    <div class="mt-4">{{ $clients->links() }}</div>
</x-admin.layout>
