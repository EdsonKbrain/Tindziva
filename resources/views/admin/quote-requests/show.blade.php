<x-admin.layout title="Pedido de proposta">
    <x-admin.page-header :title="'Pedido de '.$lead->name" :description="$lead->service?->name ?? $lead->service_name ?? 'Serviço não indicado'" :back="route('admin.quote-requests.index')" />
    @include('admin.partials.lead-show', ['assignedId' => $lead->assigned_to, 'details' => new Illuminate\Support\HtmlString(
        '<div><dt class="text-gray-500">Serviço</dt><dd class="font-semibold text-gray-900">'.e($lead->service?->name ?? $lead->service_name ?? '—').'</dd></div>'.
        '<div><dt class="text-gray-500">Orçamento</dt><dd class="font-semibold text-gray-900">'.e($lead->budget ?? '—').'</dd></div>'.
        '<div><dt class="text-gray-500">Prazo desejado</dt><dd class="font-semibold text-gray-900">'.e($lead->deadline?->format('d/m/Y') ?? '—').'</dd></div>'
    )])
</x-admin.layout>
