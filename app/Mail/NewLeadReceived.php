<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notifies the team about a new contact message or quote request.
 */
class NewLeadReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage|QuoteRequest $lead) {}

    public function envelope(): Envelope
    {
        $isQuote = $this->lead instanceof QuoteRequest;

        return new Envelope(
            replyTo: [new Address($this->lead->email, $this->lead->name)],
            subject: $isQuote
                ? 'Novo pedido de proposta: '.($this->lead->service_name ?? 'Geral')
                : 'Nova mensagem de contacto: '.($this->lead->subject ?: $this->lead->name),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.new-lead', with: [
            'isQuote' => $this->lead instanceof QuoteRequest,
            'adminUrl' => $this->lead instanceof QuoteRequest
                ? route('admin.quote-requests.show', $this->lead)
                : route('admin.contact-messages.show', $this->lead),
        ]);
    }
}
