<x-admin.layout title="Mensagem">
    <x-admin.page-header :title="$lead->subject ?: 'Mensagem de '.$lead->name" :back="route('admin.contact-messages.index')" />
    @include('admin.partials.lead-show', ['assignedId' => $lead->handled_by, 'details' => new Illuminate\Support\HtmlString(
        '<div><dt class="text-gray-500">Assunto</dt><dd class="font-semibold text-gray-900">'.e($lead->subject ?: '—').'</dd></div>'
    )])
</x-admin.layout>
