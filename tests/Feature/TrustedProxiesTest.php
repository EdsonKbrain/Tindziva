<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_spoofed_forwarded_for_header_does_not_bypass_rate_limits(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->withHeader('X-Forwarded-For', "10.0.0.{$attempt}")
                ->post('/contactos', ['name' => 'Teste', 'email' => 'a@example.com', 'message' => "Mensagem {$attempt} de teste."]);
        }

        $this->withHeader('X-Forwarded-For', '10.0.0.99')
            ->post('/contactos', ['name' => 'Teste', 'email' => 'a@example.com', 'message' => 'Mensagem extra de teste.'])
            ->assertTooManyRequests();
    }
}
