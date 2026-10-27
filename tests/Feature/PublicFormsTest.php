<?php

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Mail\NewLeadReceived;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Services\SettingsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_is_stored_and_team_is_notified(): void
    {
        Mail::fake();
        app(SettingsRepository::class)->set(['contact_email' => 'geral@tindziva.test'], 'contact');

        $this->from('/contactos')->post('/contactos', [
            'name' => 'Ana Machava',
            'email' => 'ana@example.com',
            'company' => 'Cooperativa Sol',
            'message' => 'Gostaria de saber mais sobre os vossos serviços.',
        ])->assertRedirect('/contactos#contacto')->assertSessionHas('contact_success');

        $message = ContactMessage::sole();
        $this->assertSame('Ana Machava', $message->name);
        $this->assertSame(LeadStatus::New, $message->status);
        Mail::assertSent(NewLeadReceived::class, fn (NewLeadReceived $mail) => $mail->hasTo('geral@tindziva.test'));
    }

    public function test_quote_request_records_the_selected_service(): void
    {
        $service = Service::factory()->create(['name' => 'Produção de eventos']);

        $this->from("/servicos/{$service->slug}")->post('/propostas', [
            'service_id' => $service->id,
            'name' => 'João Cossa',
            'email' => 'joao@example.com',
            'phone' => '+258 84 123 4567',
            'budget' => '50 000 – 150 000 MT',
            'message' => 'Precisamos de organizar um lançamento em Maputo.',
        ])->assertRedirect("/servicos/{$service->slug}#proposta");

        $quote = QuoteRequest::sole();
        $this->assertSame($service->id, $quote->service_id);
        $this->assertSame('Produção de eventos', $quote->service_name);
        $this->assertStringEndsWith("/servicos/{$service->slug}", $quote->source_url);
    }

    public function test_quote_request_cannot_reference_hidden_service(): void
    {
        $service = Service::factory()->hidden()->create();

        $this->post('/propostas', [
            'service_id' => $service->id,
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'phone' => '840000000',
            'message' => 'Mensagem de teste suficientemente longa.',
        ])->assertSessionHasErrors('service_id', errorBag: 'quote');

        $this->assertDatabaseCount('quote_requests', 0);
    }

    public function test_validation_errors_use_their_own_error_bag(): void
    {
        $this->post('/contactos', ['email' => 'invalido'])
            ->assertSessionHasErrors(['name', 'email', 'message'], errorBag: 'contact');
    }

    public function test_honeypot_blocks_spam(): void
    {
        $this->post('/contactos', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'Mensagem automática de spam.',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website', errorBag: 'contact');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_forms_are_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post('/contactos', ['name' => 'Teste', 'email' => 'a@example.com', 'message' => 'Mensagem número '.$attempt.' de teste.']);
        }

        $this->post('/contactos', ['name' => 'Teste', 'email' => 'a@example.com', 'message' => 'Mensagem a mais de teste.'])
            ->assertTooManyRequests();
    }

    public function test_newsletter_subscription_and_unsubscribe(): void
    {
        $this->post('/newsletter', ['email' => 'Leitor@Example.com'])->assertSessionHas('newsletter_success');
        $this->post('/newsletter', ['email' => 'leitor@example.com']);

        $subscriber = NewsletterSubscriber::sole();
        $this->assertSame('leitor@example.com', $subscriber->email);

        $this->get(route('newsletter.unsubscribe', $subscriber->token))->assertOk();
        $this->assertSame(NewsletterSubscriber::STATUS_UNSUBSCRIBED, $subscriber->fresh()->status);
    }

    public function test_newsletter_can_be_disabled_in_settings(): void
    {
        app(SettingsRepository::class)->set(['newsletter_enabled' => false], 'newsletter');

        $this->post('/newsletter', ['email' => 'leitor@example.com'])->assertForbidden();
    }
}
