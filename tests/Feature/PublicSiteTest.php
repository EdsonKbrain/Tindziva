<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Database\Seeders\ClientSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, PageSeeder::class, ServiceSeeder::class]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function staticPages(): array
    {
        return [
            'home' => ['/'],
            'about' => ['/sobre-nos'],
            'services' => ['/servicos'],
            'service' => ['/servicos/relacoes-publicas-e-assessoria-de-imprensa'],
            'portfolio' => ['/portfolio'],
            'blog' => ['/blog'],
            'contact' => ['/contactos'],
        ];
    }

    #[DataProvider('staticPages')]
    public function test_public_pages_render(string $uri): void
    {
        $this->get($uri)->assertOk()->assertSee('Tindziva', false);
    }

    public function test_home_shows_hero_from_cms_and_seo_metadata(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Transformamos ideias em comunicação que gera', false)
            ->assertSee('<span class="accent-serif">impacto</span>', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('"@type":"Organization"', false);
    }

    public function test_home_reflects_section_changes_made_in_the_cms(): void
    {
        $page = Page::forTemplate('home');
        $page->update(['sections' => array_replace_recursive($page->sections, ['hero' => ['title' => 'Novo título de teste']])]);

        $this->get('/')->assertSee('Novo título de teste');
    }

    public function test_unpublished_content_is_hidden(): void
    {
        $service = Service::factory()->hidden()->create();
        $project = Project::factory()->draft()->create();
        $post = Post::factory()->draft()->create();
        $scheduled = Post::factory()->scheduled()->create();

        $this->get("/servicos/{$service->slug}")->assertNotFound();
        $this->get("/portfolio/{$project->slug}")->assertNotFound();
        $this->get("/blog/{$post->slug}")->assertNotFound();
        $this->get("/blog/{$scheduled->slug}")->assertNotFound();
    }

    public function test_published_post_shows_author_reading_time_and_article_schema(): void
    {
        $post = Post::factory()->create(['content' => '<p>'.str_repeat('palavra ', 450).'</p>']);

        $this->get("/blog/{$post->slug}")
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->author->name)
            ->assertSee('3 min de leitura')
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('og:type" content="article"', false);
    }

    public function test_portfolio_filters_by_category_and_search(): void
    {
        $branding = ProjectCategory::factory()->create(['name' => 'Branding']);
        $events = ProjectCategory::factory()->create(['name' => 'Eventos']);
        $brandProject = Project::factory()->create(['title' => 'Identidade para cooperativa']);
        $eventProject = Project::factory()->create(['title' => 'Gala anual']);
        $brandProject->categories()->attach($branding);
        $eventProject->categories()->attach($events);

        $this->get('/portfolio?categoria='.$branding->slug)
            ->assertSee('Identidade para cooperativa')
            ->assertDontSee('Gala anual');

        $this->get('/portfolio?q=Gala')
            ->assertSee('Gala anual')
            ->assertDontSee('Identidade para cooperativa')
            ->assertSee('noindex', false);
    }

    public function test_blog_category_and_tag_listings(): void
    {
        $category = PostCategory::factory()->create();
        $inCategory = Post::factory()->create(['post_category_id' => $category->id]);
        $other = Post::factory()->create();

        $this->get("/blog/categoria/{$category->slug}")
            ->assertOk()
            ->assertSee($inCategory->title)
            ->assertDontSee($other->title);

        $this->get('/blog/etiqueta/inexistente')->assertNotFound();
    }

    public function test_blog_pagination_has_seo_links(): void
    {
        Post::factory()->count(12)->create();

        $this->get('/blog')->assertSee('rel="next"', false);
        $this->get('/blog?page=2')->assertSee('rel="prev"', false)->assertSee('Página 2');
    }

    public function test_custom_page_is_served_from_its_slug(): void
    {
        $page = Page::factory()->create(['title' => 'Política de privacidade', 'slug' => 'politica-de-privacidade']);

        $this->get('/politica-de-privacidade')->assertOk()->assertSee('Política de privacidade');

        $page->update(['is_published' => false]);
        $this->get('/politica-de-privacidade')->assertNotFound();
    }

    public function test_service_page_lists_related_projects_and_quote_form(): void
    {
        $service = Service::where('slug', 'marketing-digital')->first();
        $project = Project::factory()->create();
        $project->services()->attach($service);

        $this->get('/servicos/marketing-digital')
            ->assertOk()
            ->assertSee($project->title)
            ->assertSee('name="service_id" value="'.$service->id.'"', false);
    }

    public function test_sitemap_lists_published_content_only(): void
    {
        $published = Post::factory()->create();
        $draft = Post::factory()->draft()->create();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('services.show', 'relacoes-publicas-e-assessoria-de-imprensa'), false)
            ->assertSee($published->url(), false)
            ->assertDontSee($draft->slug, false);
    }

    public function test_robots_txt_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_missing_pages_use_custom_404(): void
    {
        $this->get('/pagina-que-nao-existe')->assertNotFound()->assertSee('Página não encontrada');
    }

    public function test_portfolio_groups_real_clients_by_segment_with_services(): void
    {
        $this->seed(ClientSeeder::class);

        $this->get('/portfolio')
            ->assertOk()
            ->assertSeeInOrder(['Micro, pequenas e médias empresas', 'Grandes empresas', 'Governo', 'Empresas públicas', 'Organizações da sociedade civil', 'Instituições de cooperação internacional', 'Projectos e marcas'])
            ->assertSee('EMOSE — Empresa Moçambicana de Seguros')
            ->assertSee('Live streaming');

        $this->get('/')->assertSee('alt="GIZ — Cooperação Alemã para o Desenvolvimento"', false);
    }

    public function test_contact_details_from_the_portfolio_are_shown(): void
    {
        $this->seed(SettingsSeeder::class);

        $this->get('/contactos')
            ->assertSee('comercial@tindziva.co.mz')
            ->assertSee('+258 84 23 66 408')
            ->assertSee('Bairro de Laulane');
    }
}
