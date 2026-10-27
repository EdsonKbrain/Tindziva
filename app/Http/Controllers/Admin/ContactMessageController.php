<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadStatus;
use App\Http\Requests\Admin\LeadUpdateRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends LeadController
{
    protected function model(): string
    {
        return ContactMessage::class;
    }

    protected function view(): string
    {
        return 'contact-messages';
    }

    protected function routePrefix(): string
    {
        return 'contact-messages';
    }

    protected function relations(): array
    {
        return ['handler'];
    }

    public function show(ContactMessage $contactMessage): View
    {
        if ($contactMessage->status === LeadStatus::New) {
            $contactMessage->update(['status' => LeadStatus::InReview]);
        }

        return $this->showLead($contactMessage);
    }

    public function update(LeadUpdateRequest $request, ContactMessage $contactMessage): RedirectResponse
    {
        return $this->updateLead($request, $contactMessage);
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        return $this->destroyLead($contactMessage);
    }

    protected function mapUpdate(array $data, Request $request): array
    {
        $data['handled_by'] = $data['assigned_to'] ?? null;
        unset($data['assigned_to']);

        if (in_array($data['status'], [LeadStatus::InContact->value, LeadStatus::Completed->value], true)) {
            $data['replied_at'] = now();
        }

        return $data;
    }

    protected function exportColumns(): array
    {
        return [
            'Data' => fn (ContactMessage $lead) => $lead->created_at->format('Y-m-d H:i'),
            'Nome' => fn (ContactMessage $lead) => $lead->name,
            'Empresa' => fn (ContactMessage $lead) => $lead->company,
            'E-mail' => fn (ContactMessage $lead) => $lead->email,
            'Telefone' => fn (ContactMessage $lead) => $lead->phone,
            'Assunto' => fn (ContactMessage $lead) => $lead->subject,
            'Mensagem' => fn (ContactMessage $lead) => $lead->message,
            'Estado' => fn (ContactMessage $lead) => $lead->status->label(),
        ];
    }
}
