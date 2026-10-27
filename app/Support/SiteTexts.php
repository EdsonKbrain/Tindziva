<?php

namespace App\Support;

use App\Services\SettingsRepository;

/**
 * Registry of the website's interface texts (menu, buttons, labels, form messages).
 *
 * Each entry is editable in the CMS under «Textos e menu». An empty value falls back to the default.
 * Types: text, textarea, url, toggle and lines (one option per line).
 */
class SiteTexts
{
    public const SETTING_PREFIX = 'text_';

    /**
     * @return array<string, array{label: string, description?: string, fields: array<string, array{label: string, default: string|bool, type?: string, help?: string}>}>
     */
    public static function groups(): array
    {
        return [
            'menu' => [
                'label' => 'Menu e cabeçalho',
                'description' => 'Nomes e visibilidade dos itens do menu principal e do botão do cabeçalho. As páginas personalizadas são adicionadas ao menu em Páginas.',
                'fields' => [
                    'nav_home' => ['label' => 'Menu: Início', 'default' => 'Início'],
                    'nav_about' => ['label' => 'Menu: Sobre nós', 'default' => 'Sobre nós'],
                    'nav_show_about' => ['label' => 'Mostrar «Sobre nós» no menu', 'default' => true, 'type' => 'toggle'],
                    'nav_services' => ['label' => 'Menu: Serviços', 'default' => 'Serviços'],
                    'nav_show_services' => ['label' => 'Mostrar «Serviços» no menu', 'default' => true, 'type' => 'toggle'],
                    'nav_services_dropdown' => ['label' => 'Mostrar a lista de serviços ao passar o rato', 'default' => true, 'type' => 'toggle'],
                    'nav_services_all' => ['label' => 'Ligação no fim da lista de serviços', 'default' => 'Ver todos os serviços'],
                    'nav_portfolio' => ['label' => 'Menu: Portfólio', 'default' => 'Portfólio'],
                    'nav_show_portfolio' => ['label' => 'Mostrar «Portfólio» no menu', 'default' => true, 'type' => 'toggle'],
                    'nav_blog' => ['label' => 'Menu: Blog', 'default' => 'Blog'],
                    'nav_show_blog' => ['label' => 'Mostrar «Blog» no menu', 'default' => true, 'type' => 'toggle'],
                    'nav_contact' => ['label' => 'Menu: Contactos', 'default' => 'Contactos'],
                    'header_cta_show' => ['label' => 'Mostrar botão no cabeçalho', 'default' => true, 'type' => 'toggle'],
                    'header_cta_label' => ['label' => 'Botão do cabeçalho — texto', 'default' => 'Pedir proposta'],
                    'header_cta_url' => ['label' => 'Botão do cabeçalho — ligação', 'default' => '/contactos#proposta', 'type' => 'url'],
                ],
            ],
            'cta' => [
                'label' => 'Chamada para acção',
                'description' => 'Bloco final apresentado nas páginas de projecto e de artigo (as páginas institucionais têm o seu próprio bloco em Páginas).',
                'fields' => [
                    'cta_title' => ['label' => 'Título', 'default' => 'Tem uma ideia? Vamos dar-lhe voz.'],
                    'cta_text' => ['label' => 'Texto', 'default' => 'Conte-nos o seu desafio. Preparamos uma proposta à medida dos seus objectivos.', 'type' => 'textarea'],
                    'cta_button_label' => ['label' => 'Botão — texto', 'default' => 'Solicitar proposta'],
                    'cta_button_url' => ['label' => 'Botão — ligação', 'default' => '/contactos#proposta', 'type' => 'url'],
                    'cta_show_whatsapp' => ['label' => 'Mostrar botão de WhatsApp (se configurado)', 'default' => true, 'type' => 'toggle'],
                    'cta_whatsapp_label' => ['label' => 'Botão de WhatsApp — texto', 'default' => 'WhatsApp'],
                ],
            ],
            'general' => [
                'label' => 'Textos gerais',
                'fields' => [
                    'breadcrumb_home' => ['label' => 'Navegação estrutural: início', 'default' => 'Início'],
                    'filter_all' => ['label' => 'Filtro: todos', 'default' => 'Todos'],
                    'search_clear' => ['label' => 'Limpar pesquisa', 'default' => 'Limpar pesquisa'],
                    'search_results' => ['label' => 'Resultados da pesquisa', 'default' => '{0} Nenhum resultado|{1} 1 resultado|[2,*] :count resultados', 'help' => 'Formato: {0} sem resultados|{1} um resultado|[2,*] :count resultados.'],
                    'search_for' => ['label' => 'Texto antes do termo pesquisado', 'default' => 'para'],
                    'reading_time' => ['label' => 'Tempo de leitura', 'default' => ':minutes min de leitura', 'help' => 'Use :minutes para o número de minutos.'],
                    'post_by' => ['label' => 'Autor do artigo', 'default' => 'Por :author', 'help' => 'Use :author para o nome.'],
                    'share_label' => ['label' => 'Partilha: título', 'default' => 'Partilhar'],
                    'share_copied' => ['label' => 'Partilha: ligação copiada', 'default' => 'Ligação copiada'],
                    'skip_to_content' => ['label' => 'Acessibilidade: saltar para o conteúdo', 'default' => 'Saltar para o conteúdo'],
                    'whatsapp_button' => ['label' => 'Botão flutuante de WhatsApp (descrição)', 'default' => 'Conversar no WhatsApp'],
                ],
            ],
            'services' => [
                'label' => 'Serviços',
                'description' => 'Textos da listagem e das páginas de cada serviço.',
                'fields' => [
                    'service_card_more' => ['label' => 'Cartões: ligação', 'default' => 'Saber mais'],
                    'services_list_more' => ['label' => 'Listagem: ligação', 'default' => 'Conhecer o serviço'],
                    'services_empty' => ['label' => 'Listagem vazia', 'default' => 'Os serviços serão publicados brevemente.'],
                    'service_eyebrow' => ['label' => 'Antetítulo (sem categoria)', 'default' => 'Serviço'],
                    'service_quote_button' => ['label' => 'Botão: pedir proposta', 'default' => 'Pedir proposta'],
                    'service_projects_button' => ['label' => 'Botão: ver projectos', 'default' => 'Ver projectos'],
                    'service_benefits_title' => ['label' => 'Caixa de benefícios: título', 'default' => 'Benefícios'],
                    'service_related_eyebrow' => ['label' => 'Projectos relacionados: antetítulo', 'default' => 'Portfólio'],
                    'service_related_title' => ['label' => 'Projectos relacionados: título', 'default' => 'Projectos relacionados'],
                    'service_quote_eyebrow' => ['label' => 'Pedido de proposta: antetítulo', 'default' => 'Proposta'],
                    'service_quote_title' => ['label' => 'Pedido de proposta: título', 'default' => 'Vamos falar sobre :service?', 'help' => 'Use :service para o nome do serviço.'],
                    'service_quote_text' => ['label' => 'Pedido de proposta: texto', 'default' => 'Partilhe os detalhes do seu projecto. Respondemos com uma proposta personalizada.', 'type' => 'textarea'],
                    'service_others_title' => ['label' => 'Outros serviços: título', 'default' => 'Outros serviços'],
                ],
            ],
            'portfolio' => [
                'label' => 'Portfólio e clientes',
                'fields' => [
                    'portfolio_search' => ['label' => 'Pesquisa: texto de ajuda', 'default' => 'Pesquisar projectos…'],
                    'portfolio_empty' => ['label' => 'Sem resultados', 'default' => 'Não encontrámos projectos com estes critérios.'],
                    'project_client' => ['label' => 'Projecto: cliente', 'default' => 'Cliente'],
                    'project_year' => ['label' => 'Projecto: ano', 'default' => 'Ano'],
                    'project_location' => ['label' => 'Projecto: local', 'default' => 'Local'],
                    'project_services' => ['label' => 'Projecto: serviços', 'default' => 'Serviços'],
                    'project_credits' => ['label' => 'Projecto: créditos', 'default' => 'Créditos'],
                    'project_related_eyebrow' => ['label' => 'Projectos relacionados: antetítulo', 'default' => 'Portfólio'],
                    'project_related_title' => ['label' => 'Projectos relacionados: título', 'default' => 'Projectos relacionados'],
                    'clients_section_show' => ['label' => 'Mostrar clientes agrupados no portfólio', 'default' => true, 'type' => 'toggle'],
                    'clients_section_eyebrow' => ['label' => 'Clientes: antetítulo', 'default' => 'Clientes e parceiros'],
                    'clients_section_title' => ['label' => 'Clientes: título', 'default' => 'Mais do que clientes, amigos que apostam a alma dos seus negócios em nós'],
                    'clients_other_segment' => ['label' => 'Clientes sem segmento', 'default' => 'Outros'],
                ],
            ],
            'blog' => [
                'label' => 'Blog',
                'fields' => [
                    'blog_search' => ['label' => 'Pesquisa: texto de ajuda', 'default' => 'Pesquisar artigos…'],
                    'blog_empty' => ['label' => 'Sem artigos', 'default' => 'Ainda não existem artigos publicados aqui.'],
                    'post_related_eyebrow' => ['label' => 'Artigos relacionados: antetítulo', 'default' => 'Continue a ler'],
                    'post_related_title' => ['label' => 'Artigos relacionados: título', 'default' => 'Artigos relacionados'],
                    'post_show_author_box' => ['label' => 'Mostrar caixa do autor no fim do artigo', 'default' => true, 'type' => 'toggle'],
                ],
            ],
            'contact' => [
                'label' => 'Contactos',
                'description' => 'Rótulos da coluna de contactos. Os dados (telefone, e-mail, morada…) editam-se em Configurações.',
                'fields' => [
                    'contact_label_email' => ['label' => 'Rótulo: e-mail', 'default' => 'E-mail'],
                    'contact_label_phone' => ['label' => 'Rótulo: telefone', 'default' => 'Telefone'],
                    'contact_label_phone_secondary' => ['label' => 'Rótulo: telefone alternativo', 'default' => 'Telefone alternativo'],
                    'contact_label_whatsapp' => ['label' => 'Rótulo: WhatsApp', 'default' => 'WhatsApp'],
                    'contact_label_address' => ['label' => 'Rótulo: endereço', 'default' => 'Endereço'],
                    'contact_label_hours' => ['label' => 'Rótulo: horário', 'default' => 'Horário'],
                    'contact_follow_us' => ['label' => 'Redes sociais: título', 'default' => 'Siga-nos'],
                ],
            ],
            'forms' => [
                'label' => 'Formulários',
                'fields' => [
                    'form_name' => ['label' => 'Campo: nome', 'default' => 'Nome'],
                    'form_company' => ['label' => 'Campo: empresa', 'default' => 'Empresa ou instituição'],
                    'form_email' => ['label' => 'Campo: e-mail', 'default' => 'E-mail'],
                    'form_phone' => ['label' => 'Campo: telefone', 'default' => 'Telefone'],
                    'form_phone_placeholder' => ['label' => 'Campo: telefone (exemplo)', 'default' => '+258 8x xxx xxxx'],
                    'form_subject' => ['label' => 'Contacto: assunto', 'default' => 'Assunto'],
                    'form_message' => ['label' => 'Contacto: mensagem', 'default' => 'Mensagem'],
                    'form_service' => ['label' => 'Proposta: serviço pretendido', 'default' => 'Serviço pretendido'],
                    'form_service_placeholder' => ['label' => 'Proposta: serviço não escolhido', 'default' => 'Ainda não sei / vários serviços'],
                    'form_budget' => ['label' => 'Proposta: orçamento', 'default' => 'Orçamento estimado'],
                    'form_budget_placeholder' => ['label' => 'Proposta: orçamento não indicado', 'default' => 'Prefiro não indicar'],
                    'form_budget_options' => ['label' => 'Proposta: opções de orçamento', 'default' => "Até 50 000 MT\n50 000 – 150 000 MT\n150 000 – 500 000 MT\nMais de 500 000 MT", 'type' => 'lines', 'help' => 'Uma opção por linha. Deixe vazio para esconder o campo.'],
                    'form_deadline' => ['label' => 'Proposta: prazo', 'default' => 'Prazo desejado'],
                    'form_project' => ['label' => 'Proposta: descrição', 'default' => 'Descreva o projecto'],
                    'form_project_placeholder' => ['label' => 'Proposta: descrição (exemplo)', 'default' => 'Objectivos, público-alvo, datas importantes…'],
                    'form_select' => ['label' => 'Listas: escolha', 'default' => 'Seleccione…'],
                    'form_privacy' => ['label' => 'Nota de privacidade', 'default' => 'Os seus dados são usados apenas para responder a este pedido.'],
                    'form_quote_submit' => ['label' => 'Botão: enviar pedido', 'default' => 'Enviar pedido'],
                    'form_contact_submit' => ['label' => 'Botão: enviar mensagem', 'default' => 'Enviar mensagem'],
                    'form_error' => ['label' => 'Mensagem de erro', 'default' => 'Verifique os campos assinalados e tente novamente.'],
                    'form_quote_success' => ['label' => 'Proposta enviada', 'default' => 'Recebemos o seu pedido de proposta. Responderemos num prazo de dois dias úteis.', 'type' => 'textarea'],
                    'form_contact_success' => ['label' => 'Mensagem enviada', 'default' => 'Obrigado pela sua mensagem. A nossa equipa entrará em contacto brevemente.', 'type' => 'textarea'],
                    'newsletter_placeholder' => ['label' => 'Newsletter: campo de e-mail', 'default' => 'O seu e-mail'],
                    'newsletter_success' => ['label' => 'Newsletter: subscrição feita', 'default' => 'Subscrição confirmada. Obrigado por acompanhar a Tindziva!'],
                    'newsletter_unsubscribed_title' => ['label' => 'Newsletter: cancelamento — título', 'default' => 'Subscrição cancelada'],
                    'newsletter_unsubscribed_text' => ['label' => 'Newsletter: cancelamento — texto', 'default' => 'Deixará de receber a nossa newsletter. Pode voltar a subscrever a qualquer momento.', 'type' => 'textarea'],
                    'newsletter_unsubscribed_button' => ['label' => 'Newsletter: cancelamento — botão', 'default' => 'Voltar ao início'],
                ],
            ],
            'footer' => [
                'label' => 'Rodapé',
                'description' => 'O texto de apresentação e os direitos de autor editam-se em Configurações.',
                'fields' => [
                    'footer_nav_title' => ['label' => 'Coluna de navegação: título', 'default' => 'Navegação'],
                    'footer_contact_title' => ['label' => 'Coluna de contactos: título', 'default' => 'Contactos'],
                    'footer_show_tagline' => ['label' => 'Mostrar slogan por baixo do logótipo', 'default' => true, 'type' => 'toggle'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, default: string|bool, type?: string, help?: string}>
     */
    public static function fields(): array
    {
        return array_merge(...array_values(array_map(fn (array $group): array => $group['fields'], self::groups())));
    }

    public static function get(string $key, array $replace = []): string
    {
        $field = self::fields()[$key] ?? null;
        $value = setting(self::SETTING_PREFIX.$key);
        $text = (string) (filled($value) ? $value : ($field['default'] ?? $key));

        foreach ($replace as $placeholder => $replacement) {
            $text = str_replace(':'.$placeholder, (string) $replacement, $text);
        }

        return $text;
    }

    public static function enabled(string $key): bool
    {
        $value = self::raw($key);

        return $value === null ? (bool) (self::fields()[$key]['default'] ?? false) : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return list<string>
     */
    public static function lines(string $key): array
    {
        $value = self::raw($key);
        $text = $value === null ? (string) (self::fields()[$key]['default'] ?? '') : (string) $value;

        return array_values(array_filter(array_map('trim', preg_split('/\R/', $text))));
    }

    /**
     * The stored value, distinguishing "never set" (null) from a value deliberately left empty.
     */
    public static function raw(string $key): ?string
    {
        $values = app(SettingsRepository::class)->all();
        $settingKey = self::SETTING_PREFIX.$key;

        return array_key_exists($settingKey, $values) ? (string) $values[$settingKey] : null;
    }
}
