@props(['title' => 'Painel'])

@use('App\Enums\Permission')
@php
    $user = auth()->user();
    $openQuotes = $user->hasPermission(Permission::ManageLeads) ? \App\Models\QuoteRequest::where('status', 'new')->count() : 0;
    $openMessages = $user->hasPermission(Permission::ManageLeads) ? \App\Models\ContactMessage::where('status', 'new')->count() : 0;

    $groups = [
        'Geral' => [
            ['label' => 'Painel', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'show' => true],
        ],
        'Conteúdos' => [
            ['label' => 'Páginas', 'route' => 'admin.pages.index', 'active' => 'admin.pages.*', 'icon' => 'file-text', 'show' => $user->can(Permission::ManagePages->value)],
            ['label' => 'Textos e menu', 'route' => 'admin.texts.edit', 'active' => 'admin.texts.*', 'icon' => 'type', 'show' => $user->can(Permission::ManagePages->value)],
            ['label' => 'Serviços', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'icon' => 'briefcase', 'show' => $user->can(Permission::ManageServices->value)],
            ['label' => 'Portfólio', 'route' => 'admin.projects.index', 'active' => 'admin.projects.*', 'icon' => 'images', 'show' => $user->can(Permission::ManageProjects->value)],
            ['label' => 'Blog', 'route' => 'admin.posts.index', 'active' => 'admin.posts.*', 'icon' => 'newspaper', 'show' => $user->can('viewAny', \App\Models\Post::class)],
            ['label' => 'Categorias', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'tags', 'show' => $user->can(Permission::ManageTaxonomies->value)],
            ['label' => 'Clientes', 'route' => 'admin.clients.index', 'active' => 'admin.clients.*', 'icon' => 'handshake', 'show' => $user->can(Permission::ManageClients->value)],
            ['label' => 'Multimédia', 'route' => 'admin.media.index', 'active' => 'admin.media.*', 'icon' => 'image', 'show' => $user->can(Permission::ManageMedia->value)],
        ],
        'Comercial' => [
            ['label' => 'Propostas', 'route' => 'admin.quote-requests.index', 'active' => 'admin.quote-requests.*', 'icon' => 'send', 'show' => $user->can(Permission::ManageLeads->value), 'badge' => $openQuotes],
            ['label' => 'Mensagens', 'route' => 'admin.contact-messages.index', 'active' => 'admin.contact-messages.*', 'icon' => 'inbox', 'show' => $user->can(Permission::ManageLeads->value), 'badge' => $openMessages],
            ['label' => 'Newsletter', 'route' => 'admin.newsletter.index', 'active' => 'admin.newsletter.*', 'icon' => 'mail', 'show' => $user->can(Permission::ManageNewsletter->value)],
        ],
        'Sistema' => [
            ['label' => 'Utilizadores', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'icon' => 'user-cog', 'show' => $user->can(Permission::ManageUsers->value)],
            ['label' => 'Configurações', 'route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'settings', 'show' => $user->can(Permission::ManageSettings->value)],
            ['label' => 'Actividade', 'route' => 'admin.activity.index', 'active' => 'admin.activity.*', 'icon' => 'activity', 'show' => $user->can(Permission::ViewActivity->value)],
        ],
    ];

    $toasts = collect(['success', 'error'])
        ->filter(fn ($type) => session()->has($type))
        ->map(fn ($type) => ['type' => $type, 'message' => session($type)])
        ->values();
@endphp

<!DOCTYPE html>
<html lang="pt-MZ" class="h-full bg-mist">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | Painel Tindziva</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
    <script>window.TindzivaAdmin = { mediaUrl: @js(route('admin.media.index')) };</script>
</head>
<body class="h-full font-sans text-gray-900" x-data="{ sidebar: false }">
    {{-- Sidebar --}}
    <div x-cloak x-show="sidebar" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" @click="sidebar = false"></div>
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-brown-900 transition-transform duration-300 lg:translate-x-0" :class="sidebar && 'translate-x-0!'" aria-label="Menu do painel">
        <div class="flex h-16 shrink-0 items-center justify-between px-6">
            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/logo-horizontal-light.png') }}" alt="Tindziva" class="h-9 w-auto"></a>
            <button type="button" class="text-white/70 lg:hidden" @click="sidebar = false" aria-label="Fechar menu"><x-lucide-x class="size-5" /></button>
        </div>
        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-4">
            @foreach ($groups as $group => $items)
                @php($visible = array_filter($items, fn ($item) => $item['show']))
                @if ($visible)
                    <div>
                        <p class="px-3 text-[11px] font-semibold tracking-widest text-white/40 uppercase">{{ $group }}</p>
                        <ul class="mt-2 space-y-0.5">
                            @foreach ($visible as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}" @class([
                                        'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                                        'bg-ochre-600 text-white' => request()->routeIs($item['active']),
                                        'text-white/70 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['active']),
                                    ])>
                                        @svg('lucide-'.$item['icon'], 'size-[18px] shrink-0')
                                        <span class="flex-1">{{ $item['label'] }}</span>
                                        @if (! empty($item['badge']))
                                            <span class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-ochre-700">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-4">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-white">
                <x-lucide-external-link class="size-[18px]" /> Ver website
            </a>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button type="button" class="-m-2 p-2 text-gray-600 lg:hidden" @click="sidebar = true" aria-label="Abrir menu"><x-lucide-menu class="size-6" /></button>
            <div class="flex-1"></div>
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                <button type="button" @click="open = !open" class="flex items-center gap-3 rounded-full py-1 pr-2 pl-1 hover:bg-gray-100" :aria-expanded="open">
                    <span class="grid size-9 place-items-center rounded-full bg-brown-800 text-xs font-semibold text-white">{{ $user->initials() }}</span>
                    <span class="hidden text-left sm:block">
                        <span class="block text-sm font-semibold">{{ $user->name }}</span>
                        <span class="block text-xs text-gray-500">{{ $user->role->label() }}</span>
                    </span>
                    <x-lucide-chevron-down class="size-4 text-gray-400" />
                </button>
                <div x-cloak x-show="open" x-transition class="absolute right-0 mt-2 w-56 rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg">
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-gray-50"><x-lucide-user class="size-4" /> O meu perfil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-700 hover:bg-red-50"><x-lucide-log-out class="size-4" /> Terminar sessão</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Flash notifications --}}
    <div x-data="toasts(@js($toasts))" class="pointer-events-none fixed inset-x-0 bottom-0 z-[70] flex flex-col items-end gap-2 p-4 sm:p-6" aria-live="polite">
        <template x-for="toast in items" :key="toast.id">
            <div x-transition class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border bg-white p-4 shadow-lg"
                 :class="toast.type === 'error' ? 'border-red-200' : 'border-green-200'">
                <span :class="toast.type === 'error' ? 'text-red-600' : 'text-green-600'">
                    <x-lucide-circle-check class="size-5" x-show="toast.type !== 'error'" />
                    <x-lucide-circle-alert class="size-5" x-show="toast.type === 'error'" />
                </span>
                <p class="flex-1 text-sm text-gray-800" x-text="toast.message"></p>
                <button type="button" @click="dismiss(toast.id)" class="text-gray-400 hover:text-gray-600" aria-label="Fechar"><x-lucide-x class="size-4" /></button>
            </div>
        </template>
    </div>

    <x-admin.media-picker />
</body>
</html>
