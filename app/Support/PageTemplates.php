<?php

namespace App\Support;

/**
 * Describes the editable sections available for each page template.
 *
 * Field types: text, textarea, richtext, url, image, icon and repeater.
 */
class PageTemplates
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'home' => 'Página inicial',
            'about' => 'Sobre nós',
            'services' => 'Serviços',
            'portfolio' => 'Portfólio',
            'blog' => 'Blog',
            'contact' => 'Contactos',
            'default' => 'Página personalizada',
        ];
    }

    /**
     * Templates that are bound to a fixed public route.
     *
     * @return list<string>
     */
    public static function system(): array
    {
        return ['home', 'about', 'services', 'portfolio', 'blog', 'contact'];
    }

    /**
     * Sections can be hidden on the website unless they declare 'toggle' => false.
     *
     * @return array<string, array{label: string, description?: string, toggle?: bool, fields: array<string, array<string, mixed>>}>
     */
    public static function sections(string $template): array
    {
        $cta = [
            'label' => 'Chamada para acção',
            'fields' => [
                'title' => ['type' => 'text', 'label' => 'Título'],
                'text' => ['type' => 'textarea', 'label' => 'Texto'],
                'button_label' => ['type' => 'text', 'label' => 'Texto do botão'],
                'button_url' => ['type' => 'url', 'label' => 'Ligação do botão'],
            ],
        ];

        $intro = [
            'label' => 'Introdução',
            'fields' => [
                'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                'title' => ['type' => 'text', 'label' => 'Título'],
                'text' => ['type' => 'textarea', 'label' => 'Texto'],
                'link_label' => ['type' => 'text', 'label' => 'Texto da ligação «ver todos»'],
            ],
        ];

        $pageHeader = [
            'label' => 'Topo da página',
            'description' => 'O título, o subtítulo e a imagem de fundo editam-se acima e na coluna lateral.',
            'toggle' => false,
            'fields' => [
                'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo (texto pequeno acima do título)'],
            ],
        ];

        $differentiators = [
            'label' => 'Diferenciais',
            'fields' => [
                'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                'title' => ['type' => 'text', 'label' => 'Título'],
                'items' => ['type' => 'repeater', 'label' => 'Diferenciais', 'fields' => [
                    'icon' => ['type' => 'icon', 'label' => 'Ícone'],
                    'title' => ['type' => 'text', 'label' => 'Título'],
                    'text' => ['type' => 'textarea', 'label' => 'Descrição'],
                ]],
            ],
        ];

        return match ($template) {
            'home' => [
                'hero' => [
                    'label' => 'Destaque principal (hero)',
                    'toggle' => false,
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'text' => ['type' => 'textarea', 'label' => 'Descrição'],
                        'primary_label' => ['type' => 'text', 'label' => 'Botão principal — texto'],
                        'primary_url' => ['type' => 'url', 'label' => 'Botão principal — ligação'],
                        'secondary_label' => ['type' => 'text', 'label' => 'Botão secundário — texto'],
                        'secondary_url' => ['type' => 'url', 'label' => 'Botão secundário — ligação'],
                        'image' => ['type' => 'image', 'label' => 'Imagem'],
                    ],
                ],
                'about' => [
                    'label' => 'Apresentação institucional',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'text' => ['type' => 'richtext', 'label' => 'Texto'],
                        'button_label' => ['type' => 'text', 'label' => 'Texto do botão'],
                        'button_url' => ['type' => 'url', 'label' => 'Ligação do botão'],
                        'image' => ['type' => 'image', 'label' => 'Imagem'],
                        'stats' => ['type' => 'repeater', 'label' => 'Números de destaque', 'fields' => [
                            'value' => ['type' => 'text', 'label' => 'Valor'],
                            'label' => ['type' => 'text', 'label' => 'Legenda'],
                        ]],
                    ],
                ],
                'services' => [...$intro, 'label' => 'Secção de serviços'],
                'portfolio' => [...$intro, 'label' => 'Secção de portfólio'],
                'clients' => [
                    'label' => 'Clientes e parceiros',
                    'description' => 'Faixa animada com os logótipos dos clientes visíveis.',
                    'fields' => [
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'link_label' => ['type' => 'text', 'label' => 'Texto da ligação «ver todos»'],
                    ],
                ],
                'differentiators' => $differentiators,
                'blog' => [...$intro, 'label' => 'Secção do blog'],
                'cta' => $cta,
            ],
            'about' => [
                'intro' => $pageHeader,
                'history' => [
                    'label' => 'História',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'text' => ['type' => 'richtext', 'label' => 'Texto'],
                        'image' => ['type' => 'image', 'label' => 'Imagem'],
                    ],
                ],
                'purpose' => [
                    'label' => 'Missão, visão e valores',
                    'fields' => [
                        'mission_label' => ['type' => 'text', 'label' => 'Missão — título'],
                        'mission' => ['type' => 'textarea', 'label' => 'Missão'],
                        'vision_label' => ['type' => 'text', 'label' => 'Visão — título'],
                        'vision' => ['type' => 'textarea', 'label' => 'Visão'],
                        'values_eyebrow' => ['type' => 'text', 'label' => 'Valores — antetítulo'],
                        'values_title' => ['type' => 'text', 'label' => 'Valores — título'],
                        'values' => ['type' => 'repeater', 'label' => 'Valores', 'fields' => [
                            'title' => ['type' => 'text', 'label' => 'Valor'],
                            'text' => ['type' => 'textarea', 'label' => 'Descrição'],
                        ]],
                    ],
                ],
                'approach' => [
                    'label' => 'Abordagem de trabalho',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                        'step_label' => ['type' => 'text', 'label' => 'Rótulo das etapas (ex.: ETAPA)'],
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'text' => ['type' => 'textarea', 'label' => 'Texto'],
                        'steps' => ['type' => 'repeater', 'label' => 'Etapas', 'fields' => [
                            'title' => ['type' => 'text', 'label' => 'Etapa'],
                            'text' => ['type' => 'textarea', 'label' => 'Descrição'],
                        ]],
                    ],
                ],
                'differentiators' => $differentiators,
                'team' => [
                    'label' => 'Equipa',
                    'description' => 'A secção só aparece no website quando existir pelo menos um membro.',
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'Antetítulo'],
                        'title' => ['type' => 'text', 'label' => 'Título'],
                        'text' => ['type' => 'textarea', 'label' => 'Texto'],
                        'members' => ['type' => 'repeater', 'label' => 'Membros', 'fields' => [
                            'name' => ['type' => 'text', 'label' => 'Nome'],
                            'role' => ['type' => 'text', 'label' => 'Função'],
                            'photo' => ['type' => 'image', 'label' => 'Fotografia'],
                        ]],
                    ],
                ],
                'cta' => $cta,
            ],
            'services', 'portfolio' => [
                'intro' => $pageHeader,
                'cta' => $cta,
            ],
            'blog' => [
                'intro' => $pageHeader,
            ],
            'contact' => [
                'intro' => $pageHeader,
                'details' => [
                    'toggle' => false,
                    'label' => 'Informação de contacto',
                    'fields' => [
                        'form_title' => ['type' => 'text', 'label' => 'Título do formulário de contacto'],
                        'quote_title' => ['type' => 'text', 'label' => 'Título do pedido de proposta'],
                        'hours' => ['type' => 'textarea', 'label' => 'Horário de atendimento'],
                    ],
                ],
            ],
            default => [
                'intro' => $pageHeader,
            ],
        };
    }

    /**
     * Icons offered in the editor for icon fields.
     *
     * @return list<string>
     */
    public static function icons(): array
    {
        return [
            'sparkles', 'megaphone', 'newspaper', 'pen-tool', 'calendar-days', 'book-open', 'target', 'lightbulb',
            'users', 'handshake', 'trending-up', 'shield-check', 'globe', 'heart', 'award', 'rocket',
            'message-circle', 'camera', 'mic', 'palette', 'chart-bar', 'compass', 'zap', 'star',
        ];
    }
}
