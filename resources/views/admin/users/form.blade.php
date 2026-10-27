@php
    $selectedPermissions = old('permissions', $user->permissions ?? array_map(fn ($permission) => $permission->value, App\Enums\Role::Admin->defaultPermissions()));
@endphp

<x-admin.layout :title="$user->exists ? 'Editar utilizador' : 'Novo utilizador'">
    <x-admin.page-header :title="$user->exists ? $user->name : 'Novo utilizador'" :back="route('admin.users.index')">
        @if ($user->exists)
            <form method="POST" action="{{ route('admin.users.reset-link', $user) }}">
                @csrf
                <x-admin.button variant="secondary" icon="send">Enviar ligação de redefinição</x-admin.button>
            </form>
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="grid gap-6 xl:grid-cols-3"
          x-data="{ role: @js(old('role', $user->role?->value)), customize: @js((bool) old('customize_permissions', $user->permissions !== null)) }">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="space-y-6 xl:col-span-2">
            <x-admin.card title="Dados pessoais">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="name" label="Nome" :value="$user->name" required />
                    <x-admin.input name="email" type="email" label="E-mail" :value="$user->email" required />
                    <x-admin.input name="job_title" label="Função" :value="$user->job_title" placeholder="Ex.: Gestora de comunicação" />
                    <x-admin.textarea name="bio" label="Biografia" :value="$user->bio" rows="3" class="sm:col-span-2" help="Apresentada no fim dos artigos do autor." />
                </div>
            </x-admin.card>

            <x-admin.card title="Perfil de acesso">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($roles as $role)
                        <label class="flex cursor-pointer gap-3 rounded-lg border p-4 transition" :class="role === @js($role->value) ? 'border-ochre-600 bg-ochre-50/60 ring-1 ring-ochre-600' : 'border-gray-200 hover:border-gray-300'">
                            <input type="radio" name="role" value="{{ $role->value }}" x-model="role" class="mt-0.5 border-gray-300 text-ochre-600 focus:ring-ochre-600">
                            <span>
                                <span class="block text-sm font-semibold text-gray-900">{{ $role->label() }}</span>
                                <span class="block text-xs text-gray-500">{{ $role->description() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

                <div x-show="role === 'admin'" x-cloak class="mt-6 border-t border-gray-100 pt-5">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-800">
                        <input type="checkbox" name="customize_permissions" value="1" x-model="customize" class="rounded border-gray-300 text-ochre-600 focus:ring-ochre-600">
                        Personalizar as permissões deste administrador
                    </label>
                    <div x-show="customize" class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->value }}" @checked(in_array($permission->value, $selectedPermissions, true)) class="rounded border-gray-300 text-ochre-600 focus:ring-ochre-600">
                                {{ $permission->label() }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Acesso">
                <div class="space-y-5">
                    <x-admin.toggle name="is_active" label="Conta activa" :checked="$user->is_active" help="Contas inactivas são desligadas de imediato." />
                    <x-admin.input name="password" type="password" :label="$user->exists ? 'Nova palavra-passe' : 'Palavra-passe'" autocomplete="new-password"
                        :help="$user->exists ? 'Deixe vazio para manter a actual.' : 'Deixe vazio para enviar um convite por e-mail (recomendado).'" />
                    <x-admin.input name="password_confirmation" type="password" label="Confirmar palavra-passe" autocomplete="new-password" />
                    <x-admin.button class="w-full" icon="check">Guardar</x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </form>
</x-admin.layout>
