<x-admin.layout title="Utilizadores">
    <x-admin.page-header title="Utilizadores" description="Contas com acesso ao painel e respectivos perfis.">
        <x-admin.button :href="route('admin.users.create')" icon="plus">Novo utilizador</x-admin.button>
    </x-admin.page-header>

    <x-admin.filters placeholder="Pesquisar por nome ou e-mail…">
        <select name="role" class="admin-input sm:w-48" aria-label="Perfil">
            <option value="">Todos os perfis</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
    </x-admin.filters>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                    <tr><th class="px-5 py-3">Utilizador</th><th class="px-5 py-3">Perfil</th><th class="px-5 py-3">Artigos</th><th class="px-5 py-3">Último acesso</th><th class="px-5 py-3"><span class="sr-only">Acções</span></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-9 place-items-center rounded-full bg-brown-800 text-xs font-semibold text-white">{{ $user->initials() }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $user->name }} @if ($user->is(auth()->user()))<span class="text-xs font-normal text-gray-500">(você)</span>@endif</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <x-admin.badge :color="$user->isSuperAdmin() ? 'purple' : 'gray'">{{ $user->role->label() }}</x-admin.badge>
                                @unless ($user->is_active)<x-admin.badge color="red">Inactivo</x-admin.badge>@endunless
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->posts_count }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-block rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700" title="Editar"><x-lucide-pencil class="size-4" /></a>
                                @endcan
                                @can('delete', $user)
                                    <x-admin.delete-button :action="route('admin.users.destroy', $user)" icon-only confirm="Eliminar esta conta? Os artigos do utilizador ficarão sem autor." />
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
</x-admin.layout>
