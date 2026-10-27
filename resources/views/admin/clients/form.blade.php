<x-admin.layout :title="$client->exists ? 'Editar cliente' : 'Novo cliente'">
    <x-admin.page-header :title="$client->exists ? $client->name : 'Novo cliente ou parceiro'" :back="route('admin.clients.index')" />

    <form method="POST" action="{{ $client->exists ? route('admin.clients.update', $client) : route('admin.clients.store') }}" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($client->exists) @method('PUT') @endif
        <div class="space-y-6 xl:col-span-2">
            <x-admin.card>
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="name" label="Nome" :value="$client->name" required class="sm:col-span-2" />
                    <x-admin.select name="type" label="Tipo" :options="collect($types)->mapWithKeys(fn ($type) => [$type->value => $type->label()])" :value="$client->type?->value" />
                    <x-admin.select name="segment" label="Segmento" :options="collect($segments)->mapWithKeys(fn ($segment) => [$segment->value => $segment->label()])" :value="$client->segment?->value" placeholder="Sem segmento" />
                    <x-admin.input name="website" type="url" label="Website" :value="$client->website" placeholder="https://" />
                    <x-admin.input name="services_provided" label="Serviços prestados" :value="implode(', ', $client->services_provided ?? [])" class="sm:col-span-2" placeholder="Assessoria de imprensa, Produção audiovisual" help="Separe por vírgulas. Apresentados no portfólio, por baixo do logótipo." />
                    <x-admin.textarea name="description" label="Notas" :value="$client->description" rows="3" class="sm:col-span-2" />
                </div>
            </x-admin.card>
            @if ($client->exists && $client->projects->isNotEmpty())
                <x-admin.card title="Projectos associados" :padding="false">
                    <ul class="divide-y divide-gray-100">
                        @foreach ($client->projects as $project)
                            <li><a href="{{ route('admin.projects.edit', $project) }}" class="block px-5 py-3 text-sm hover:bg-gray-50">{{ $project->title }}</a></li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </div>
        <div class="space-y-6">
            <x-admin.card title="Apresentação">
                <div class="space-y-5">
                    <x-admin.media-field name="logo_id" label="Logótipo" :media="$client->logo" help="PNG com fundo transparente recomendado." />
                    <x-admin.toggle name="is_visible" label="Mostrar no website" :checked="$client->is_visible" />
                    <x-admin.input name="sort_order" type="number" min="0" label="Ordem" :value="$client->sort_order ?? 0" />
                    <x-admin.button class="w-full" icon="check">Guardar</x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </form>
    @if ($client->exists)
        <div class="mt-6"><x-admin.delete-button :action="route('admin.clients.destroy', $client)" confirm="Eliminar este cliente? Os projectos associados serão mantidos." /></div>
    @endif
</x-admin.layout>
