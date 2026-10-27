<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\Role;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Demonstration content so the website can be reviewed before real content is added.
 * Every demo project is clearly labelled as an example and should be replaced.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);

        $author = User::firstOrCreate(
            ['email' => 'equipa@tindziva.invalid'],
            [
                'name' => 'Equipa Tindziva',
                'password' => Hash::make(Str::password(40)),
                'role' => Role::Author,
                'is_active' => false,
                'job_title' => 'Comunicação & Ideias',
                'bio' => 'Artigos escritos pela equipa da Tindziva Comunicação & Ideias.',
            ],
        );

        $projectCategories = collect(['Assessoria de imprensa', 'Branding', 'Eventos', 'Digital', 'Editorial'])
            ->mapWithKeys(fn (string $name, int $index) => [$name => ProjectCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $index])]);

        $services = Service::pluck('id', 'slug');

        $projects = [
            ['Campanha de lançamento de marca', 'Branding', 'publicidade-e-branding', 'Criação de identidade visual e campanha de lançamento para uma nova marca de consumo.'],
            ['Conferência anual de parceiros', 'Eventos', 'producao-de-eventos', 'Conceito, logística e comunicação de uma conferência com convidados nacionais e internacionais.'],
            ['Estratégia de redes sociais institucional', 'Digital', 'marketing-digital', 'Planeamento editorial e gestão de comunidade para uma instituição de referência.'],
            ['Cobertura mediática de projecto social', 'Assessoria de imprensa', 'relacoes-publicas-e-assessoria-de-imprensa', 'Plano de imprensa e relação com os media para divulgar um projecto com impacto comunitário.'],
            ['Edição de relatório de investigação', 'Editorial', 'edicao-literaria-e-cientifica', 'Revisão científica, paginação e produção gráfica de um relatório técnico.'],
            ['Lançamento de obra literária', 'Eventos', 'edicao-literaria-e-cientifica', 'Edição de texto e organização da cerimónia de lançamento de um livro de autor moçambicano.'],
        ];

        foreach ($projects as $index => [$title, $category, $service, $excerpt]) {
            $project = Project::firstOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'excerpt' => $excerpt.' (Projecto de demonstração.)',
                'description' => "<h2>O desafio</h2><p>{$excerpt}</p><h2>A nossa abordagem</h2><p>Começámos por compreender os objectivos e os públicos do cliente, definimos as mensagens-chave e articulámos as diferentes áreas da equipa para garantir uma execução coerente.</p><h2>Resultados</h2><p>Substitua este texto pelos resultados reais do projecto, como alcance, cobertura mediática ou participação.</p>",
                'location' => 'Maputo, Moçambique',
                'completed_at' => now()->subMonths(($index + 1) * 3),
                'show_client' => false,
                'show_credits' => true,
                'credits' => [['role' => 'Direcção de projecto', 'name' => 'Tindziva Comunicação & Ideias']],
                'is_featured' => $index < 4,
                'is_published' => true,
                'published_at' => now()->subMonths(($index + 1) * 3),
                'sort_order' => $index,
            ]);
            $project->categories()->syncWithoutDetaching([$projectCategories[$category]->id]);
            if (isset($services[$service])) {
                $project->services()->syncWithoutDetaching([$services[$service]]);
            }
        }

        $postCategories = collect(['Comunicação', 'Marketing digital', 'Eventos', 'Edição'])
            ->mapWithKeys(fn (string $name, int $index) => [$name => PostCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'sort_order' => $index])]);

        foreach ($this->articles() as $index => $article) {
            $post = Post::firstOrCreate(['slug' => Str::slug($article['title'])], [
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'content' => $article['content'],
                'user_id' => $author->id,
                'post_category_id' => $postCategories[$article['category']]->id,
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(($index + 1) * 9),
                'is_featured' => $index === 0,
            ]);
            $post->tags()->syncWithoutDetaching(collect($article['tags'])->map(fn (string $tag) => Tag::firstOrCreate(['slug' => Str::slug($tag)], ['name' => $tag])->id)->all());
        }
    }

    /**
     * @return list<array{title: string, excerpt: string, category: string, tags: list<string>, content: string}>
     */
    private function articles(): array
    {
        return [
            [
                'title' => 'Cinco erros a evitar num comunicado de imprensa',
                'excerpt' => 'Um bom comunicado é claro, relevante e fácil de transformar em notícia. Veja os erros mais comuns e como evitá-los.',
                'category' => 'Comunicação',
                'tags' => ['imprensa', 'comunicados'],
                'content' => '<p>O comunicado de imprensa continua a ser uma das ferramentas mais eficazes para chegar aos jornalistas. Mas, num contexto em que as redacções recebem dezenas de mensagens por dia, só os comunicados bem preparados chegam a ser publicados.</p><h2>1. Esconder a notícia</h2><p>O primeiro parágrafo deve responder às perguntas essenciais: quem, o quê, quando, onde e porquê. Se o jornalista tiver de ler até ao fim para perceber a novidade, o comunicado perde-se.</p><h2>2. Usar linguagem publicitária</h2><p>Adjectivos excessivos e frases promocionais reduzem a credibilidade. Prefira factos, dados e citações concretas.</p><h2>3. Esquecer o contexto</h2><p>Explique porque é que o tema é relevante para o público do meio de comunicação. A relevância local faz a diferença.</p><h2>4. Não indicar contactos</h2><p>Inclua sempre um contacto disponível para entrevistas, esclarecimentos e envio de imagens.</p><h2>5. Enviar a todos da mesma forma</h2><p>Segmente a lista de contactos e adapte o envio a cada editoria. Um envio bem dirigido vale mais do que centenas de envios genéricos.</p><blockquote>Um comunicado de imprensa é, antes de mais, um serviço prestado ao jornalista.</blockquote>',
            ],
            [
                'title' => 'Branding: muito mais do que um logótipo',
                'excerpt' => 'A marca é a soma de todas as experiências que as pessoas têm com a sua organização. Saiba por onde começar.',
                'category' => 'Comunicação',
                'tags' => ['branding', 'identidade visual'],
                'content' => '<p>Quando se fala de marca, muitas pessoas pensam imediatamente no logótipo. No entanto, o logótipo é apenas a parte mais visível de um sistema muito mais amplo.</p><h2>Propósito e posicionamento</h2><p>Antes do design, é preciso clareza: o que a organização faz, para quem e porque é diferente. Estas respostas orientam todas as decisões seguintes.</p><h2>Identidade verbal</h2><p>O tom de voz, as mensagens-chave e a forma de falar com cada público são tão importantes como as cores e a tipografia.</p><h2>Consistência</h2><p>Um manual de normas garante que a marca é aplicada da mesma forma em todos os suportes — do cartão de visita às redes sociais.</p>',
            ],
            [
                'title' => 'Como planear a comunicação digital da sua organização',
                'excerpt' => 'Objectivos claros, públicos bem definidos e um calendário editorial são a base de uma presença digital consistente.',
                'category' => 'Marketing digital',
                'tags' => ['redes sociais', 'estratégia digital'],
                'content' => '<p>Estar presente nas redes sociais não é, por si só, uma estratégia. Para gerar resultados, a comunicação digital precisa de planeamento.</p><h2>Defina objectivos mensuráveis</h2><p>Notoriedade, geração de contactos ou apoio ao cliente exigem abordagens diferentes. Escolha indicadores que permitam avaliar o progresso.</p><h2>Conheça os seus públicos</h2><p>Que plataformas utilizam, em que horários e que conteúdos valorizam? A resposta determina o formato e a frequência das publicações.</p><h2>Crie um calendário editorial</h2><p>Planeie temas, datas importantes e campanhas com antecedência. Assim garante regularidade e evita publicações improvisadas.</p><h2>Meça e ajuste</h2><p>Analise os resultados mensalmente e ajuste a estratégia. O digital permite aprender rapidamente com os dados.</p>',
            ],
            [
                'title' => 'Guia prático para organizar um evento corporativo',
                'excerpt' => 'Do conceito à avaliação final, estas são as etapas essenciais para um evento bem-sucedido.',
                'category' => 'Eventos',
                'tags' => ['eventos', 'planeamento'],
                'content' => '<p>Um evento corporativo é uma oportunidade única para reforçar relações e comunicar uma mensagem de forma memorável. O segredo está no planeamento.</p><h2>Comece pelo objectivo</h2><p>Lançar um produto, celebrar um marco ou reunir parceiros? O objectivo orienta o formato, o local e a lista de convidados.</p><h2>Prepare o orçamento e o cronograma</h2><p>Identifique todas as rubricas — espaço, catering, técnica, comunicação — e defina prazos para cada fase.</p><h2>Pense na experiência do convidado</h2><p>Acolhimento, sinalética, programa e momentos de interacção fazem a diferença na percepção final.</p><h2>Comunique antes, durante e depois</h2><p>Convites, cobertura em tempo real e partilha de resultados prolongam o impacto do evento.</p>',
            ],
            [
                'title' => 'Porque é que a revisão editorial faz a diferença',
                'excerpt' => 'Um texto revisto transmite credibilidade e respeito pelo leitor. Conheça as diferentes fases da revisão.',
                'category' => 'Edição',
                'tags' => ['edição', 'revisão'],
                'content' => '<p>Seja um livro, um relatório institucional ou um artigo científico, a qualidade do texto influencia directamente a forma como a mensagem é recebida.</p><h2>Revisão linguística</h2><p>Corrige ortografia, gramática e pontuação, garantindo um texto correcto e fluido.</p><h2>Revisão de estilo</h2><p>Assegura coerência de linguagem, clareza das ideias e adequação ao público-alvo.</p><h2>Normalização científica</h2><p>Nas publicações académicas, verifica citações, referências bibliográficas e cumprimento das normas exigidas.</p><h2>Revisão de provas</h2><p>Depois da paginação, uma última leitura garante que nada se perdeu entre o texto e o documento final.</p>',
            ],
            [
                'title' => 'Comunicação de crise: preparar antes de precisar',
                'excerpt' => 'As organizações mais bem preparadas para uma crise são as que planearam a resposta com antecedência.',
                'category' => 'Comunicação',
                'tags' => ['imprensa', 'gestão de crise'],
                'content' => '<p>Nenhuma organização está imune a uma crise. A diferença está na capacidade de responder de forma rápida, transparente e coordenada.</p><h2>Identifique os riscos</h2><p>Faça um levantamento dos cenários mais prováveis e do impacto que cada um poderia ter na reputação.</p><h2>Defina papéis e porta-vozes</h2><p>Quem decide, quem fala e quem valida? Um comité de crise com responsabilidades claras evita hesitações.</p><h2>Prepare mensagens-base</h2><p>Ter declarações preliminares preparadas permite reagir nas primeiras horas, quando a pressão é maior.</p><h2>Treine a equipa</h2><p>Simulações e media training preparam os porta-vozes para responder com serenidade e credibilidade.</p>',
            ],
        ];
    }
}
