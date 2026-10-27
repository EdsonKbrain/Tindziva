<x-auth-layout title="Recuperar palavra-passe">
    <p class="mt-2 text-ink/70">Indique o seu e-mail. Enviaremos uma ligação segura para definir uma nova palavra-passe.</p>

    @if (session('status'))
        <x-site.alert class="mt-6">{{ __(session('status')) }}</x-site.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf
        <x-site.field name="email" type="email" label="E-mail" required autofocus autocomplete="username" />
        <x-site.button class="w-full justify-center" icon="send">Enviar ligação</x-site.button>
    </form>
    <p class="mt-6 text-sm"><a href="{{ route('login') }}" class="font-medium text-ochre-700">Voltar ao início de sessão</a></p>
</x-auth-layout>
