<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Create the system pages that back the fixed public routes.
     */
    public function run(): void
    {
        $differentiators = [
            'eyebrow' => 'Porquê a Tindziva',
            'title' => 'O que nos *distingue*',
            'items' => [
                ['icon' => 'globe', 'title' => 'Genuinamente moçambicana', 'text' => 'Desde 2019 no mercado nacional, conhecemos os media, as instituições e os públicos com quem a sua marca precisa de falar.'],
                ['icon' => 'handshake', 'title' => 'Soluções personalizadas', 'text' => 'Uma abordagem centrada no cliente, alinhada com as necessidades e os objectivos de cada empresa ou projecto.'],
                ['icon' => 'target', 'title' => 'Resultados tangíveis', 'text' => 'Combinamos recursos técnicos e desígnios estratégicos para gerar resultados que se medem.'],
                ['icon' => 'users', 'title' => 'Equipa talentosa e experiente', 'text' => 'Comunicação, marketing, design, audiovisual, eventos e edição reunidos numa só equipa.'],
            ],
        ];

        $pages = [
            [
                'template' => 'home',
                'title' => 'Página inicial',
                'slug' => 'inicio',
                'sections' => [
                    'hero' => [
                        'eyebrow' => 'Comunicação & Marketing · Moçambique, desde 2019',
                        'title' => 'Transformamos ideias em comunicação que gera *impacto*.',
                        'text' => 'Somos a ponte que precisa entre a sua marca e o seu público. Estratégias inovadoras e criativas, pensadas para impulsionar o sucesso dos nossos parceiros e clientes.',
                        'primary_label' => 'Pedir proposta',
                        'primary_url' => '/contactos#proposta',
                        'secondary_label' => 'Ver portfólio',
                        'secondary_url' => '/portfolio',
                        'image' => null,
                    ],
                    'about' => [
                        'eyebrow' => 'Quem somos',
                        'title' => 'Criando *abraços* e harmonizando laços',
                        'text' => '<p>A Tindziva é uma empresa genuinamente moçambicana que desde 2019 actua nos segmentos de <strong>Comunicação</strong> e <strong>Marketing</strong>, dedicada a impulsionar o sucesso dos nossos parceiros e clientes através de estratégias inovadoras e criativas.</p><p>Com uma equipa talentosa e experiente, oferecemos <strong>soluções personalizadas</strong> que geram <strong>resultados tangíveis</strong> — do desenvolvimento de campanhas impactantes à gestão da presença online.</p>',
                        'button_label' => 'Conheça a Tindziva',
                        'button_url' => '/sobre-nos',
                        'image' => null,
                        'stats' => [
                            ['value' => '2019', 'label' => 'no mercado moçambicano desde'],
                            ['value' => '8', 'label' => 'áreas de serviço integradas'],
                            ['value' => '+35', 'label' => 'clientes e parceiros'],
                        ],
                    ],
                    'services' => ['eyebrow' => 'Serviços', 'title' => 'Um mosaico de *ideias*', 'text' => 'Que atravessa a Comunicação, o Marketing, a Publicidade e a Gestão de eventos.'],
                    'portfolio' => ['eyebrow' => 'Portfólio', 'title' => 'O universo de ideias que mais *comunicaram*', 'text' => 'Uma selecção de trabalhos desenvolvidos com os nossos clientes e parceiros.'],
                    'clients' => ['title' => 'Mais do que clientes, amigos que apostam a alma dos seus negócios em nós'],
                    'differentiators' => $differentiators,
                    'blog' => ['eyebrow' => 'Blog', 'title' => 'Ideias e *perspectivas*', 'text' => 'Reflexões, guias práticos e tendências do mundo da comunicação.'],
                    'cta' => [
                        'title' => 'Traga os seus sonhos. Nós moldamos novas realidades.',
                        'text' => 'Conte-nos o seu desafio. Preparamos uma proposta à medida dos seus objectivos e do seu orçamento.',
                        'button_label' => 'Solicitar proposta',
                        'button_url' => '/contactos#proposta',
                    ],
                ],
                'meta_title' => 'Tindziva Comunicação & Ideias — Agência de comunicação em Moçambique',
            ],
            [
                'template' => 'about',
                'title' => 'Sobre nós',
                'slug' => 'sobre-nos',
                'subtitle' => 'Uma empresa genuinamente moçambicana de comunicação e marketing, ao serviço do sucesso dos nossos parceiros e clientes desde 2019.',
                'sections' => [
                    'history' => [
                        'eyebrow' => 'A nossa história',
                        'title' => 'Criando *abraços* e harmonizando laços',
                        'text' => '<p>A Tindziva é uma empresa genuinamente moçambicana que desde 2019 actua nos segmentos de <strong>Comunicação</strong> e <strong>Marketing</strong>, dedicada a impulsionar o sucesso dos nossos parceiros e clientes, através de estratégias inovadoras e criativas.</p><p>Com uma equipa talentosa e experiente, estamos comprometidos em oferecer <strong>soluções personalizadas</strong> que geram <strong>resultados tangíveis</strong>. Desde o desenvolvimento de campanhas impactantes à gestão de presença online, a nossa abordagem centrada no cliente visa atender às necessidades específicas de cada empresa ou projecto, alinhando-se sempre aos objectivos de cada negócio.</p><p>Combinando <strong>recursos técnicos</strong> e <strong>desígnios estratégicos</strong>, estamos prontos para ajudar a sua empresa a destacar-se num mercado competitivo e em constante evolução.</p><p>Hoje trabalhamos com micro, pequenas e médias empresas, grandes empresas, Governo, empresas públicas, organizações da sociedade civil e instituições de cooperação internacional.</p>',
                        'image' => null,
                    ],
                    'purpose' => [
                        'mission' => 'Capacitar empresas e marcas a alcançarem o máximo potencial através de estratégias de comunicação e marketing inovadoras, eficazes e eficientes.',
                        'vision' => 'Ser reconhecida como líder inovadora no sector de comunicação e marketing, transformando desafios em oportunidades para os nossos parceiros e clientes.',
                        'values' => [
                            ['title' => 'Inovação', 'text' => 'Procuramos novas ideias e novas formas de comunicar.'],
                            ['title' => 'Integridade', 'text' => 'Agimos com transparência e honramos os compromissos assumidos.'],
                            ['title' => 'Excelência', 'text' => 'Cuidamos de cada detalhe para entregar trabalho de qualidade.'],
                            ['title' => 'Foco no cliente', 'text' => 'Os objectivos de cada cliente orientam todas as nossas decisões.'],
                        ],
                    ],
                    'approach' => [
                        'title' => 'Como *trabalhamos*',
                        'text' => 'Um processo simples e colaborativo, do primeiro contacto à avaliação dos resultados.',
                        'steps' => [
                            ['title' => 'Escutar', 'text' => 'Compreendemos os objectivos, os públicos e o contexto de cada organização.'],
                            ['title' => 'Planear', 'text' => 'Definimos a estratégia, as mensagens-chave, os canais e o calendário.'],
                            ['title' => 'Criar e executar', 'text' => 'Produzimos conteúdos, campanhas e eventos com qualidade e atenção ao detalhe.'],
                            ['title' => 'Medir', 'text' => 'Acompanhamos os indicadores e partilhamos relatórios para melhorar continuamente.'],
                        ],
                    ],
                    'differentiators' => [...$differentiators, 'eyebrow' => 'Diferenciais'],
                    'team' => ['title' => 'As pessoas por detrás das *ideias*', 'text' => null, 'members' => []],
                    'cta' => ['title' => 'Vamos construir algo juntos?', 'text' => 'Fale connosco e descubra como podemos ajudar a sua organização a comunicar melhor.', 'button_label' => 'Falar connosco', 'button_url' => '/contactos'],
                ],
            ],
            [
                'template' => 'services',
                'title' => 'Os nossos *serviços*',
                'slug' => 'servicos',
                'subtitle' => 'Um mosaico de ideias que atravessa a Comunicação, o Marketing, a Publicidade e a Gestão de eventos.',
                'sections' => ['cta' => ['title' => 'Não sabe por onde começar?', 'text' => 'Diga-nos o que pretende alcançar. Nós ajudamos a escolher a melhor combinação de serviços.', 'button_label' => 'Pedir aconselhamento', 'button_url' => '/contactos#proposta']],
                'meta_title' => 'Serviços de comunicação',
            ],
            [
                'template' => 'portfolio',
                'title' => 'Trabalho que *comunica*',
                'slug' => 'portfolio',
                'subtitle' => 'O universo de ideias que mais comunicaram: campanhas, eventos, publicações e estratégias desenvolvidas com os nossos clientes e parceiros.',
                'sections' => ['cta' => ['title' => 'O próximo projecto pode ser o seu.', 'text' => 'Vamos conversar sobre os seus objectivos.', 'button_label' => 'Pedir proposta', 'button_url' => '/contactos#proposta']],
                'meta_title' => 'Portfólio',
            ],
            [
                'template' => 'blog',
                'title' => 'Ideias que *inspiram*',
                'slug' => 'blog',
                'subtitle' => 'Artigos, guias e reflexões sobre comunicação, marketing, eventos e edição.',
                'sections' => ['cta' => []],
                'meta_title' => 'Blog',
            ],
            [
                'template' => 'contact',
                'title' => 'Vamos *conversar*',
                'slug' => 'contactos',
                'subtitle' => 'Traga todos os seus sonhos, e nós prometemos moldar novas realidades. Peça uma proposta ou envie-nos uma mensagem.',
                'sections' => ['details' => ['form_title' => 'Enviar mensagem', 'quote_title' => 'Pedir proposta', 'hours' => null]],
                'meta_title' => 'Contactos',
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::withTrashed()->firstOrNew(['template' => $data['template'], 'is_system' => true]);

            if ($page->exists) {
                continue;
            }

            $page->forceFill([...$data, 'is_system' => true, 'is_published' => true])->save();
        }
    }
}
