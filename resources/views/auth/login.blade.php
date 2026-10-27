<x-auth-layout title="Iniciar sessão">
    <p class="mt-2 text-ink/70">Aceda ao painel de gestão do website.</p>

    @if (session('status'))
        <x-site.alert class="mt-6">{{ session('status') }}</x-site.alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf
        <x-site.field name="email" type="email" label="E-mail" required autofocus autocomplete="username" />
        <div>
            <x-site.field name="password" type="password" label="Palavra-passe" required autocomplete="current-password" />
            <div class="mt-2 text-right">
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-ochre-700 hover:text-ochre-800">Esqueceu a palavra-passe?</a>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-ink/80">
            <input type="checkbox" name="remember" class="rounded border-brown-200 text-ochre-600 focus:ring-ochre-600"> Manter sessão iniciada
        </label>
        <x-site.button class="w-full justify-center" icon="log-in">Entrar</x-site.button>
    </form>
</x-auth-layout>
