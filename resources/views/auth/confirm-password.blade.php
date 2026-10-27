<x-auth-layout title="Confirmar palavra-passe">
    <p class="mt-2 text-ink/70">Por segurança, confirme a sua palavra-passe para continuar.</p>

    <form method="POST" action="{{ route('password.confirm.store') }}" class="mt-8 space-y-5">
        @csrf
        <x-site.field name="password" type="password" label="Palavra-passe" required autofocus autocomplete="current-password" />
        <x-site.button class="w-full justify-center">Confirmar</x-site.button>
    </form>
</x-auth-layout>
