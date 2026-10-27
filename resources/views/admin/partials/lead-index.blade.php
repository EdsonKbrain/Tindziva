{{-- Shared listing for contact messages and quote requests --}}
<div class="mb-4 flex gap-2 overflow-x-auto pb-1">
    <a href="{{ route("admin.{$routePrefix}.index") }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium whitespace-nowrap', 'bg-brown-800 text-white' => ! request('status'), 'bg-white text-gray-700 ring-1 ring-gray-200' => request('status')])>Todos ({{ $counts->sum() }})</a>
    @foreach ($statuses as $status)
        <a href="{{ route("admin.{$routePrefix}.index", ['status' => $status->value]) }}" @class(['rounded-full px-3 py-1.5 text-sm font-medium whitespace-nowrap', 'bg-brown-800 text-white' => request('status') === $status->value, 'bg-white text-gray-700 ring-1 ring-gray-200' => request('status') !== $status->value])>{{ $status->label() }} ({{ $counts[$status->value] ?? 0 }})</a>
    @endforeach
</div>

<x-admin.filters placeholder="Pesquisar por nome, e-mail, empresa…">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="date" name="from" value="{{ request('from') }}" class="admin-input sm:w-40" aria-label="Desde">
    <input type="date" name="to" value="{{ request('to') }}" class="admin-input sm:w-40" aria-label="Até">
</x-admin.filters>

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                <tr><th class="px-5 py-3">Contacto</th><th class="px-5 py-3">{{ $routePrefix === 'quote-requests' ? 'Serviço' : 'Assunto' }}</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3">Recebido</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($leads as $lead)
                    <tr class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route("admin.{$routePrefix}.show", $lead) }}'">
                        <td class="px-5 py-3">
                            <a href="{{ route("admin.{$routePrefix}.show", $lead) }}" @class(['text-gray-900', 'font-bold' => $lead->status === App\Enums\LeadStatus::New, 'font-semibold' => $lead->status !== App\Enums\LeadStatus::New])>{{ $lead->name }}</a>
                            <p class="text-xs text-gray-500">{{ $lead->company ? $lead->company.' · ' : '' }}{{ $lead->email }}</p>
                        </td>
                        <td class="max-w-xs truncate px-5 py-3 text-gray-600">
                            {{ $routePrefix === 'quote-requests' ? ($lead->service?->name ?? $lead->service_name ?? 'Geral') : ($lead->subject ?: \Illuminate\Support\Str::limit($lead->message, 60)) }}
                        </td>
                        <td class="px-5 py-3"><x-admin.badge :color="$lead->status->color()">{{ $lead->status->label() }}</x-admin.badge></td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-600" title="{{ $lead->created_at->format('d/m/Y H:i') }}">{{ $lead->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <x-admin.empty :colspan="4" icon="inbox" title="Sem registos" />
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $leads->links() }}</div>
