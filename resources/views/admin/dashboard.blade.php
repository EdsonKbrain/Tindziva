<x-admin.layout title="Painel">
    <x-admin.page-header :title="'Olá, '.explode(' ', auth()->user()->name)[0]" description="Resumo da actividade do website." >
        @can('create', App\Models\Post::class)
            <x-admin.button :href="route('admin.posts.create')" icon="plus">Novo artigo</x-admin.button>
        @endcan
    </x-admin.page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Artigos" :value="$stats['posts']" icon="newspaper" :hint="$stats['published'].' publicados · '.$stats['drafts'].' rascunhos · '.$stats['scheduled'].' agendados'" :href="route('admin.posts.index')" />
        @can(App\Enums\Permission::ManageProjects->value)
            <x-admin.stat label="Projectos no portfólio" :value="$stats['projects']" icon="images" :href="route('admin.projects.index')" />
        @endcan
        @can(App\Enums\Permission::ManageServices->value)
            <x-admin.stat label="Serviços activos" :value="$stats['services']" icon="briefcase" :href="route('admin.services.index')" />
        @endcan
        @can(App\Enums\Permission::ManageLeads->value)
            <x-admin.stat label="Pedidos de proposta" :value="$stats['quotes']" icon="send" :hint="$stats['openQuotes'].' em aberto'" :href="route('admin.quote-requests.index')" />
            <x-admin.stat label="Mensagens por responder" :value="$stats['unreadMessages']" icon="inbox" :href="route('admin.contact-messages.index')" />
        @endcan
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            @can(App\Enums\Permission::ManageLeads->value)
                <x-admin.card title="Últimos pedidos de proposta" :padding="false">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($latestQuotes as $quote)
                            <li>
                                <a href="{{ route('admin.quote-requests.show', $quote) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $quote->name }} @if ($quote->company)<span class="font-normal text-gray-500">· {{ $quote->company }}</span>@endif</p>
                                        <p class="truncate text-xs text-gray-500">{{ $quote->service?->name ?? $quote->service_name ?? 'Serviço não indicado' }} · {{ $quote->created_at->diffForHumans() }}</p>
                                    </div>
                                    <x-admin.badge :color="$quote->status->color()">{{ $quote->status->label() }}</x-admin.badge>
                                </a>
                            </li>
                        @empty
                            <li><x-admin.empty icon="send" title="Ainda não há pedidos de proposta" /></li>
                        @endforelse
                    </ul>
                </x-admin.card>

                <x-admin.card title="Mensagens recentes" :padding="false">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($latestMessages as $message)
                            <li>
                                <a href="{{ route('admin.contact-messages.show', $message) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $message->name }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $message->subject ?: \Illuminate\Support\Str::limit($message->message, 70) }}</p>
                                    </div>
                                    <x-admin.badge :color="$message->status->color()">{{ $message->status->label() }}</x-admin.badge>
                                </a>
                            </li>
                        @empty
                            <li><x-admin.empty icon="inbox" title="Sem mensagens" /></li>
                        @endforelse
                    </ul>
                </x-admin.card>
            @endcan

            <x-admin.card title="Artigos actualizados recentemente" :padding="false">
                <ul class="divide-y divide-gray-100">
                    @forelse ($recentPosts as $post)
                        <li>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $post->author?->name }} · {{ $post->updated_at->diffForHumans() }}</p>
                                </div>
                                <x-admin.badge :color="$post->status->color()">{{ $post->status->label() }}</x-admin.badge>
                            </a>
                        </li>
                    @empty
                        <li><x-admin.empty icon="newspaper" title="Ainda não há artigos" /></li>
                    @endforelse
                </ul>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Estatísticas de visitas">
                @if ($analyticsEnabled)
                    <p class="text-sm text-gray-600">O Google Analytics está activo ({{ setting('analytics_id') }}).</p>
                    <x-admin.button href="https://analytics.google.com/" target="_blank" rel="noopener" variant="secondary" icon="bar-chart-3" class="mt-4">Abrir Google Analytics</x-admin.button>
                @else
                    <p class="text-sm text-gray-600">As estatísticas de visitas ficam disponíveis após configurar a integração com o Google Analytics.</p>
                    @can(App\Enums\Permission::ManageSettings->value)
                        <x-admin.button :href="route('admin.settings.edit').'#seo'" variant="secondary" icon="settings" class="mt-4">Configurar</x-admin.button>
                    @endcan
                @endif
            </x-admin.card>

            @can(App\Enums\Permission::ViewActivity->value)
                <x-admin.card title="Actividade recente" :padding="false">
                    <ul class="divide-y divide-gray-100">
                        @forelse ($activity as $log)
                            <li class="px-5 py-3">
                                <p class="text-sm text-gray-800"><span class="font-semibold">{{ $log->user?->name ?? 'Sistema' }}</span> {{ lcfirst($log->description) }}</p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                            </li>
                        @empty
                            <li><x-admin.empty icon="activity" title="Sem actividade registada" /></li>
                        @endforelse
                    </ul>
                    <div class="border-t border-gray-100 px-5 py-3"><a href="{{ route('admin.activity.index') }}" class="text-sm font-semibold text-ochre-700">Ver todo o registo</a></div>
                </x-admin.card>
            @endcan
        </div>
    </div>
</x-admin.layout>
