<x-admin.layout title="Mensagens">
    <x-admin.page-header title="Mensagens de contacto" description="Mensagens enviadas através do formulário de contacto.">
        <x-admin.button :href="route('admin.contact-messages.export', request()->only('status'))" variant="secondary" icon="download">Exportar CSV</x-admin.button>
    </x-admin.page-header>
    @include('admin.partials.lead-index')
</x-admin.layout>
