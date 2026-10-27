<?php

namespace Database\Seeders;

use App\Services\SettingsRepository;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Default identity settings. Contacts and social networks are left empty to be filled in the CMS.
     */
    public function run(SettingsRepository $settings): void
    {
        $defaults = [
            'general' => [
                'company_name' => 'Tindziva Comunicação & Ideias',
                'company_tagline' => 'A ponte que precisa…',
            ],
            'contact' => [
                'contact_email' => 'comercial@tindziva.co.mz',
                'contact_phone' => '+258 84 23 66 408',
                'contact_phone_secondary' => '+258 87 93 66 408',
                'contact_address' => "Avenida Cardeal Alexandre dos Santos, Q.º 53, Casa n.º 163\nBairro de Laulane — Distrito Municipal KaMavota\nMaputo, Moçambique",
            ],
            'footer' => [
                'footer_text' => 'Empresa genuinamente moçambicana que, desde 2019, actua nos segmentos de Comunicação e Marketing. A ponte que precisa entre a sua marca e o seu público.',
                'footer_copyright' => 'Tindziva Comunicação & Ideias. Todos os direitos reservados.',
            ],
            'seo' => [
                'seo_title' => 'Tindziva Comunicação & Ideias — Agência de comunicação em Moçambique',
                'seo_description' => 'Empresa moçambicana de comunicação e marketing desde 2019: estratégia, relações públicas e assessoria de imprensa, marketing digital, eventos, publicidade, audiovisual e edição.',
            ],
            'whatsapp' => [
                'whatsapp_enabled' => true,
                'whatsapp_number' => '258842366408',
                'whatsapp_message' => 'Olá! Gostaria de saber mais sobre os serviços da Tindziva.',
            ],
            'newsletter' => [
                'newsletter_enabled' => true,
                'newsletter_title' => 'Newsletter',
                'newsletter_text' => 'Ideias, tendências e novidades da comunicação em Moçambique, directamente no seu e-mail.',
            ],
        ];

        foreach ($defaults as $group => $values) {
            $existing = $settings->all();
            $settings->set(array_diff_key($values, array_filter($existing, fn ($value) => $value !== null)), $group);
        }
    }
}
