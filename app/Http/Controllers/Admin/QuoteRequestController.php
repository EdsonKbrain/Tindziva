<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LeadUpdateRequest;
use App\Models\QuoteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuoteRequestController extends LeadController
{
    protected function model(): string
    {
        return QuoteRequest::class;
    }

    protected function view(): string
    {
        return 'quote-requests';
    }

    protected function routePrefix(): string
    {
        return 'quote-requests';
    }

    protected function relations(): array
    {
        return ['service', 'assignee'];
    }

    public function show(QuoteRequest $quoteRequest): View
    {
        return $this->showLead($quoteRequest);
    }

    public function update(LeadUpdateRequest $request, QuoteRequest $quoteRequest): RedirectResponse
    {
        return $this->updateLead($request, $quoteRequest);
    }

    public function destroy(QuoteRequest $quoteRequest): RedirectResponse
    {
        return $this->destroyLead($quoteRequest);
    }

    protected function exportColumns(): array
    {
        return [
            'Data' => fn (QuoteRequest $lead) => $lead->created_at->format('Y-m-d H:i'),
            'Nome' => fn (QuoteRequest $lead) => $lead->name,
            'Empresa' => fn (QuoteRequest $lead) => $lead->company,
            'E-mail' => fn (QuoteRequest $lead) => $lead->email,
            'Telefone' => fn (QuoteRequest $lead) => $lead->phone,
            'Serviço' => fn (QuoteRequest $lead) => $lead->service?->name ?? $lead->service_name,
            'Orçamento' => fn (QuoteRequest $lead) => $lead->budget,
            'Prazo' => fn (QuoteRequest $lead) => $lead->deadline?->format('Y-m-d'),
            'Mensagem' => fn (QuoteRequest $lead) => $lead->message,
            'Estado' => fn (QuoteRequest $lead) => $lead->status->label(),
            'Responsável' => fn (QuoteRequest $lead) => $lead->assignee?->name,
        ];
    }
}
