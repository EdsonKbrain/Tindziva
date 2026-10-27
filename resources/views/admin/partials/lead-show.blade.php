{{-- Shared detail view for contact messages and quote requests --}}
<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-admin.card title="Pedido">
            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-500">Nome</dt><dd class="font-semibold text-gray-900">{{ $lead->name }}</dd></div>
                <div><dt class="text-gray-500">Empresa</dt><dd class="font-semibold text-gray-900">{{ $lead->company ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">E-mail</dt><dd><a href="mailto:{{ $lead->email }}" class="font-semibold text-ochre-700 hover:underline">{{ $lead->email }}</a></dd></div>
                <div><dt class="text-gray-500">Telefone</dt><dd>@if ($lead->phone)<a href="tel:{{ preg_replace('/\s+/', '', $lead->phone) }}" class="font-semibold text-ochre-700 hover:underline">{{ $lead->phone }}</a>@else — @endif</dd></div>
                {{ $details ?? '' }}
                <div><dt class="text-gray-500">Data de submissão</dt><dd class="font-semibold text-gray-900">{{ $lead->created_at->format('d/m/Y \à\s H:i') }}</dd></div>
            </dl>
            <div class="mt-6 border-t border-gray-100 pt-5">
                <p class="text-sm text-gray-500">Mensagem</p>
                <p class="mt-2 whitespace-pre-line text-gray-800">{{ $lead->message }}</p>
            </div>
        </x-admin.card>
        <div class="flex flex-wrap gap-2">
            <x-admin.button :href="'mailto:'.$lead->email.'?subject='.rawurlencode('Re: '.($lead->subject ?? $lead->service_name ?? 'Tindziva'))" icon="mail">Responder por e-mail</x-admin.button>
            @if ($lead->phone)
                <x-admin.button :href="'https://wa.me/'.preg_replace('/\D+/', '', $lead->phone)" target="_blank" rel="noopener" variant="secondary" icon="message-circle">WhatsApp</x-admin.button>
            @endif
        </div>
    </div>
    <div class="space-y-6">
        <x-admin.card title="Seguimento">
            <form method="POST" action="{{ route("admin.{$routePrefix}.update", $lead) }}" class="space-y-4">
                @csrf @method('PUT')
                <x-admin.select name="status" label="Estado" :options="collect($statuses)->mapWithKeys(fn ($status) => [$status->value => $status->label()])" :value="$lead->status->value" />
                <x-admin.select name="assigned_to" label="Responsável" :options="$users->pluck('name', 'id')" :value="$assignedId" placeholder="Ninguém" />
                <x-admin.textarea name="internal_notes" label="Notas internas" :value="$lead->internal_notes" rows="6" help="Visíveis apenas para a equipa." />
                <x-admin.button class="w-full" icon="check">Actualizar</x-admin.button>
            </form>
        </x-admin.card>
        <x-admin.card title="Informação técnica">
            <dl class="space-y-2 text-xs text-gray-500">
                @isset($lead->source_url)<div><dt class="inline font-semibold">Origem:</dt> <dd class="inline break-all">{{ $lead->source_url }}</dd></div>@endisset
                <div><dt class="inline font-semibold">IP:</dt> <dd class="inline">{{ $lead->ip_address ?? '—' }}</dd></div>
            </dl>
        </x-admin.card>
        <x-admin.delete-button :action="route("admin.{$routePrefix}.destroy", $lead)" confirm="Eliminar definitivamente este registo?" />
    </div>
</div>
