<x-admin.layout title="O meu perfil">
    <x-admin.page-header title="O meu perfil" :description="$user->role->label().' · '.$user->email" />

    <div class="grid max-w-4xl gap-6">
        <x-admin.card title="Informação pessoal">
            @if (session('status') === 'profile-information-updated')
                <p class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-800">Perfil actualizado.</p>
            @endif
            <form method="POST" action="{{ route('user-profile-information.update') }}" class="grid gap-5 sm:grid-cols-2">
                @csrf @method('PUT')
                <div>
                    <label for="name" class="admin-label">Nome</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="admin-input">
                    @error('name', 'updateProfileInformation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="admin-label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="admin-input">
                    @error('email', 'updateProfileInformation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2"><x-admin.button icon="check">Guardar</x-admin.button></div>
            </form>
        </x-admin.card>

        <x-admin.card title="Alterar palavra-passe" description="Ao alterar a palavra-passe, as sessões noutros dispositivos são terminadas.">
            @if (session('status') === 'password-updated')
                <p class="mb-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-800">Palavra-passe alterada.</p>
            @endif
            <form method="POST" action="{{ route('user-password.update') }}" class="grid gap-5 sm:grid-cols-3">
                @csrf @method('PUT')
                @foreach (['current_password' => 'Palavra-passe actual', 'password' => 'Nova palavra-passe', 'password_confirmation' => 'Confirmar'] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="admin-label">{{ $label }}</label>
                        <input id="{{ $field }}" type="password" name="{{ $field }}" required autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}" class="admin-input">
                        @error($field, 'updatePassword')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div class="sm:col-span-3"><x-admin.button icon="check">Alterar palavra-passe</x-admin.button></div>
            </form>
        </x-admin.card>
    </div>
</x-admin.layout>
