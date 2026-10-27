<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * The eight service lines presented in the Tindziva 2024 portfolio.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Comunicação e marketing estratégico',
                'slug' => 'comunicacao-e-marketing-estrategico',
                'icon' => 'target',
                'excerpt' => 'Planos de comunicação e marketing integrados, incluindo identidade visual e análise de mercado.',
                'description' => '<h2>Estratégia antes da acção</h2><p>Desenvolvemos planos de comunicação e marketing integrados, alinhados com os objectivos de cada negócio ou projecto. Partimos da análise do mercado e dos públicos para definir mensagens, canais e prioridades.</p><h3>O que inclui</h3><ul><li>Diagnóstico e análise de mercado</li><li>Planos de comunicação e marketing integrados</li><li>Criação e revisão de identidade visual</li><li>Estratégia de comunicação e consultoria em imagem</li><li>Consultoria de marketing</li></ul>',
                'benefits' => ['Decisões de comunicação orientadas por objectivos', 'Mensagens e imagem consistentes', 'Conhecimento do mercado e dos públicos', 'Recursos aplicados onde geram mais resultado'],
            ],
            [
                'name' => 'Relações públicas e assessoria de imprensa',
                'slug' => 'relacoes-publicas-e-assessoria-de-imprensa',
                'icon' => 'newspaper',
                'excerpt' => 'Gestão da relação com a imprensa, comunicados e eventos que geram cobertura mediática positiva.',
                'description' => '<h2>A sua história, contada nos meios certos</h2><p>Fazemos a gestão do relacionamento com a imprensa, criamos conteúdos para comunicados e organizamos momentos que geram cobertura mediática positiva e com impacto.</p><h3>O que inclui</h3><ul><li>Redacção e distribuição de comunicados de imprensa</li><li>Relação com jornalistas e órgãos de comunicação social</li><li>Conferências e encontros com a imprensa</li><li>Relações públicas e gestão de reputação</li><li>Acompanhamento da cobertura mediática</li></ul>',
                'benefits' => ['Maior visibilidade nos media', 'Relação de confiança com jornalistas', 'Reputação acompanhada de perto', 'Mensagens credíveis e consistentes'],
            ],
            [
                'name' => 'Marketing digital',
                'slug' => 'marketing-digital',
                'icon' => 'trending-up',
                'excerpt' => 'SEO, SEM, redes sociais, e-mail marketing e marketing de conteúdo para aproximar a marca do seu público.',
                'description' => '<h2>Presença digital com estratégia</h2><p>Implementamos estratégias de marketing online que tornam a sua marca visível e relevante para o público certo.</p><h3>O que inclui</h3><ul><li>Optimização para motores de busca (SEO)</li><li>Publicidade em motores de busca (SEM)</li><li>Gestão de redes sociais</li><li>E-mail marketing</li><li>Marketing de conteúdo</li><li>Criação de websites</li></ul>',
                'benefits' => ['Comunidade online mais envolvida', 'Campanhas segmentadas e mensuráveis', 'Conteúdos alinhados com a identidade da marca', 'Resultados acompanhados com dados'],
            ],
            [
                'name' => 'Produção de eventos',
                'slug' => 'producao-de-eventos',
                'icon' => 'calendar-days',
                'excerpt' => 'Serviço completo, do planeamento à execução, para que cada evento seja memorável e bem-sucedido.',
                'description' => '<h2>Eventos que deixam marca</h2><p>Oferecemos um serviço completo, desde o planeamento à execução, para garantir que cada evento é memorável e bem-sucedido.</p><h3>O que inclui</h3><ul><li>Conceito, planeamento e gestão logística</li><li>Lançamentos, conferências e cerimónias</li><li>Mestre de cerimónias e animação cultural</li><li>Cobertura fotográfica, vídeo e live streaming</li><li>Comunicação antes, durante e depois do evento</li></ul>',
                'benefits' => ['Um único parceiro do início ao fim', 'Organização rigorosa', 'Experiências memoráveis para os convidados', 'Cobertura e divulgação integradas'],
            ],
            [
                'name' => 'Publicidade e branding',
                'slug' => 'publicidade-e-branding',
                'icon' => 'megaphone',
                'excerpt' => 'Campanhas publicitárias criativas para media tradicional e digital, com foco no público-alvo.',
                'description' => '<h2>Campanhas que chegam ao público certo</h2><p>Criamos campanhas publicitárias impactantes e criativas, tanto para os media tradicionais como para o digital, sempre com foco em alcançar o público-alvo.</p><h3>O que inclui</h3><ul><li>Conceito criativo e campanhas publicitárias</li><li>Inserção de publicidade em televisão, rádio e imprensa</li><li>Publicidade digital</li><li>Branding e impressão gráfica</li><li>Planeamento de meios</li></ul>',
                'benefits' => ['Mensagens criativas e memoráveis', 'Presença nos meios tradicionais e digitais', 'Marca reconhecível em todos os suportes', 'Foco no público-alvo'],
            ],
            [
                'name' => 'Activação de marcas',
                'slug' => 'activacao-de-marcas',
                'icon' => 'rocket',
                'excerpt' => 'Lançamentos de produtos, feiras, exposições e patrocínios desportivos e culturais.',
                'description' => '<h2>Pôr a marca em contacto com as pessoas</h2><p>Concebemos e executamos acções de activação que aumentam a notoriedade da marca e criam experiências directas com o público.</p><h3>O que inclui</h3><ul><li>Lançamentos de produtos</li><li>Feiras e exposições</li><li>Patrocínios de eventos desportivos e culturais</li><li>Acções promocionais e de proximidade</li></ul>',
                'benefits' => ['Maior notoriedade da marca', 'Contacto directo com o público', 'Associação a momentos culturais e desportivos', 'Experiências que geram conversa'],
            ],
            [
                'name' => 'Design gráfico e produção audiovisual',
                'slug' => 'design-grafico-e-producao-audiovisual',
                'icon' => 'camera',
                'excerpt' => 'Materiais gráficos, vídeos promocionais e conteúdo visual de alta qualidade para a sua marca.',
                'description' => '<h2>Imagem que fortalece a marca</h2><p>Desenvolvemos materiais gráficos, vídeos promocionais e conteúdo visual de alta qualidade para fortalecer a identidade da marca e envolver o público.</p><h3>O que inclui</h3><ul><li>Design gráfico para impressão e digital</li><li>Vídeos institucionais e promocionais</li><li>Cobertura fotográfica e audiovisual</li><li>Live streaming</li><li>Impressão gráfica</li></ul>',
                'benefits' => ['Conteúdo visual profissional', 'Identidade da marca reforçada', 'Formatos prontos para todos os canais', 'Produção do conceito à entrega final'],
            ],
            [
                'name' => 'Edição literária e científica',
                'slug' => 'edicao-literaria-e-cientifica',
                'icon' => 'book-open',
                'excerpt' => 'Edição e revisão linguística de livros, revistas, brochuras, boletins e publicações técnicas.',
                'description' => '<h2>Textos claros, correctos e cuidados</h2><p>A edição e a revisão linguística abrangem livros, revistas, brochuras e boletins. Acompanhamos autores, publicações e instituições na criação e no tratamento editorial dos seus conteúdos.</p><h3>O que inclui</h3><ul><li>Criação de conteúdo e produção textual</li><li>Edição de conteúdos</li><li>Revisão editorial e linguística</li><li>Revistas, livros, brochuras e boletins</li></ul>',
                'benefits' => ['Textos correctos e coerentes', 'Linguagem adequada a cada público', 'Publicações com qualidade editorial', 'Acompanhamento do texto à publicação'],
            ],
        ];

        foreach ($services as $position => $data) {
            Service::withTrashed()->firstOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'sort_order' => $position + 1, 'is_published' => true, 'is_featured' => true],
            );
        }
    }
}
