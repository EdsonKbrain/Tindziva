<x-admin.layout title="Pedidos de proposta">
    <x-admin.page-header title="Pedidos de proposta" description="Pedidos comerciais recebidos através do website.">
        <x-admin.button :href="route('admin.quote-requests.export', request()->only('status'))" variant="secondary" icon="download">Exportar CSV</x-admin.button>
    </x-admin.page-header>
    @include('admin.partials.lead-index')
</x-admin.layout>
