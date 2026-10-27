<x-admin.layout title="Newsletter">
    <x-admin.page-header title="Newsletter" :description="$totalActive.' subscritores activos.'">
        <x-admin.button :href="route('admin.newsletter.export')" variant="secondary" icon="download">Exportar activos (CSV)</x-admin.button>
        @can(App\Enums\Permission::ManageSettings->value)
            <x-admin.button :href="route('admin.settings.edit').'#newsletter'" variant="ghost" icon="settings">Configurar</x-admin.button>
        @endcan
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar e-mail…">
        <select name="status" class="admin-input sm:w-44" aria-label="Estado">
            <option value="">Todos</option>
            <option value="subscribed" @selected(request('status') === 'subscribed')>Subscritos</option>
            <option value="unsubscribed" @selected(request('status') === 'unsubscribed')>Cancelados</option>
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                <tr><th class="px-5 py-3">E-mail</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3">Data</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($subscribers as $subscriber)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $subscriber->email }} @if ($subscriber->name)<span class="font-normal text-gray-500">· {{ $subscriber->name }}</span>@endif</td>
                        <td class="px-5 py-3"><x-admin.badge :color="$subscriber->status === 'subscribed' ? 'green' : 'gray'">{{ $subscriber->status === 'subscribed' ? 'Subscrito' : 'Cancelado' }}</x-admin.badge></td>
                        <td class="px-5 py-3 text-gray-600">{{ $subscriber->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-right"><x-admin.delete-button :action="route('admin.newsletter.destroy', $subscriber)" icon-only confirm="Remover este subscritor?" /></td>
                    </tr>
                @empty
                    <x-admin.empty :colspan="4" icon="mail" title="Sem subscritores" />
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $subscribers->links() }}</div>
</x-admin.layout>
