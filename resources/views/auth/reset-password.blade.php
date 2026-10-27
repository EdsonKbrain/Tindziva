<x-auth-layout title="Definir palavra-passe">
    <p class="mt-2 text-ink/70">Mínimo de 10 caracteres, com maiúsculas, minúsculas e números.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-site.field name="email" type="email" label="E-mail" required :value="$request->email" autocomplete="username" />
        <x-site.field name="password" type="password" label="Nova palavra-passe" required autocomplete="new-password" />
        <x-site.field name="password_confirmation" type="password" label="Confirmar palavra-passe" required autocomplete="new-password" />
        <x-site.button class="w-full justify-center" icon="check">Guardar palavra-passe</x-site.button>
    </form>
</x-auth-layout>
