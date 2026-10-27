<?php

namespace Tests\Feature;

use App\Enums\ClientSegment;
use App\Enums\LeadStatus;
use App\Enums\PostStatus;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use App\Services\MediaService;
use Database\Seeders\PageSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, PageSeeder::class, ServiceSeeder::class]);
        $this->editor = User::factory()->editor()->create();
    }

    public function test_every_admin_screen_renders_for_a_super_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $post = Post::factory()->create();
        $project = Project::factory()->create();
        $client = Client::factory()->create();
        $quote = QuoteRequest::factory()->create();
        $page = Page::forTemplate('about');

        $uris = [
            '/admin', '/admin/perfil', '/admin/texts', '/admin/pages', '/admin/pages/create', "/admin/pages/{$page->id}/edit",
            '/admin/services', '/admin/services/create', '/admin/services/'.Service::first()->id.'/edit',
            '/admin/projects', '/admin/projects/create', "/admin/projects/{$project->id}/edit",
            '/admin/posts', '/admin/posts/create', "/admin/posts/{$post->id}/edit", "/admin/posts/{$post->id}/preview",
            '/admin/clients', '/admin/clients/create', "/admin/clients/{$client->id}/edit",
            '/admin/categories', '/admin/categories/projects', '/admin/categories/tags',
            '/admin/media', '/admin/quote-requests', "/admin/quote-requests/{$quote->id}", '/admin/contact-messages',
            '/admin/newsletter', '/admin/users', '/admin/users/create', '/admin/settings', '/admin/activity',
            '/admin/pages/'.Page::forTemplate('home')->id.'/edit',
        ];

        foreach ($uris as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    public function test_service_crud_is_reflected_on_the_website(): void
    {
        $this->actingAs($this->editor)->post('/admin/services', [
            'name' => 'Consultoria de comunicação',
            'excerpt' => 'Diagnóstico e plano estratégico.',
            'description' => '<p>Texto <strong>rico</strong></p><script>alert(1)</script>',
            'benefits' => ['Clareza', '', 'Foco'],
            'icon' => 'compass',
            'is_published' => 1,
        ])->assertRedirect();

        $service = Service::where('slug', 'consultoria-de-comunicacao')->sole();
        $this->assertSame(['Clareza', 'Foco'], $service->benefits);
        $this->assertStringNotContainsString('<script', $service->description);

        $this->get('/servicos')->assertSee('Consultoria de comunicação');
        $this->get('/servicos/consultoria-de-comunicacao')->assertOk()->assertSee('Clareza');

        $this->actingAs($this->editor)->delete("/admin/services/{$service->id}");
        $this->assertSoftDeleted($service);
        $this->get('/servicos/consultoria-de-comunicacao')->assertNotFound();

        $this->actingAs($this->editor)->post("/admin/services/{$service->id}/restore");
        $this->assertNotSoftDeleted($service);
    }

    public function test_project_saves_categories_gallery_videos_and_credits(): void
    {
        $category = ProjectCategory::factory()->create();
        $client = Client::factory()->create(['name' => 'Banco Exemplo']);
        $images = Media::factory()->count(3)->create();

        $this->actingAs($this->editor)->post('/admin/projects', [
            'title' => 'Campanha nacional',
            'client_id' => $client->id,
            'categories' => [$category->id],
            'services' => [Service::first()->id],
            'cover_image_id' => $images[0]->id,
            'gallery' => [$images[2]->id, $images[1]->id],
            'video_urls' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'credits' => [['role' => 'Fotografia', 'name' => 'Estúdio Luz'], ['role' => '', 'name' => '']],
            'show_client' => 1,
            'show_credits' => 1,
            'is_published' => 1,
        ])->assertRedirect();

        $project = Project::where('slug', 'campanha-nacional')->sole();
        $this->assertSame([$images[2]->id, $images[1]->id], $project->gallery->modelKeys());
        $this->assertCount(1, $project->credits);
        $this->assertNotNull($project->published_at);

        $this->get('/portfolio/campanha-nacional')
            ->assertOk()
            ->assertSee('Banco Exemplo')
            ->assertSee('Estúdio Luz')
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_project_hides_client_and_credits_without_authorisation(): void
    {
        $project = Project::factory()->create([
            'client_id' => Client::factory()->create(['name' => 'Cliente Confidencial'])->id,
            'credits' => [['role' => 'Design', 'name' => 'Pessoa Reservada']],
        ]);

        $this->get("/portfolio/{$project->slug}")->assertDontSee('Cliente Confidencial')->assertDontSee('Pessoa Reservada');
    }

    public function test_unsupported_video_links_are_rejected(): void
    {
        $this->actingAs($this->editor)->post('/admin/projects', [
            'title' => 'Vídeo inválido',
            'video_urls' => ['https://example.com/video.mp4'],
        ])->assertSessionHasErrors('video_urls.0');
    }

    public function test_editor_publishes_post_with_tags(): void
    {
        $this->actingAs($this->editor)->post('/admin/posts', [
            'title' => 'Tendências de comunicação',
            'content' => '<p>Conteúdo</p><img src="x" onerror="alert(1)">',
            'status' => PostStatus::Published->value,
            'tags' => 'Branding, imprensa, branding',
        ])->assertRedirect();

        $post = Post::where('slug', 'tendencias-de-comunicacao')->sole();
        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame($this->editor->id, $post->user_id);
        $this->assertStringNotContainsString('onerror', $post->content);
        $this->assertEqualsCanonicalizing(['branding', 'imprensa'], $post->tags->pluck('slug')->all());
        $this->assertSame(2, Tag::count());

        $this->get('/blog/tendencias-de-comunicacao')->assertOk();
    }

    public function test_scheduled_posts_require_future_date_and_are_published_by_scheduler(): void
    {
        $this->actingAs($this->editor)->post('/admin/posts', [
            'title' => 'Agendado no passado',
            'status' => PostStatus::Scheduled->value,
            'published_at' => now()->subHour()->format('Y-m-d H:i'),
        ])->assertSessionHasErrors('published_at');

        $this->actingAs($this->editor)->post('/admin/posts', [
            'title' => 'Artigo agendado',
            'status' => PostStatus::Scheduled->value,
            'published_at' => now()->addHour()->format('Y-m-d H:i'),
        ])->assertRedirect();

        $post = Post::where('slug', 'artigo-agendado')->sole();
        $this->get('/blog/artigo-agendado')->assertNotFound();

        $this->artisan('posts:publish-scheduled')->assertSuccessful();
        $this->assertSame(PostStatus::Scheduled, $post->fresh()->status);

        $this->travel(2)->hours();
        $this->artisan('posts:publish-scheduled')->assertSuccessful();

        $this->assertSame(PostStatus::Published, $post->fresh()->status);
        $this->get('/blog/artigo-agendado')->assertOk();
    }

    public function test_preview_shows_drafts_to_authorised_users_only(): void
    {
        $draft = Post::factory()->draft()->create(['title' => 'Rascunho secreto']);

        $this->actingAs($this->editor)->get("/admin/posts/{$draft->id}/preview")->assertOk()->assertSee('Pré-visualização')->assertSee('noindex', false);
        $this->actingAs(User::factory()->author()->create())->get("/admin/posts/{$draft->id}/preview")->assertForbidden();
    }

    public function test_page_sections_are_sanitised_and_unknown_keys_dropped(): void
    {
        $home = Page::forTemplate('home');
        $image = Media::factory()->create();

        $this->actingAs($this->editor)->put("/admin/pages/{$home->id}", [
            'title' => 'Página inicial',
            'sections' => [
                'hero' => ['title' => '<b>Comunicação</b> que *inspira*', 'primary_url' => 'javascript:alert(1)', 'image' => $image->id],
                'about' => ['text' => '<p>Olá</p><script>x</script>', 'stats' => [['value' => '10', 'label' => 'anos'], ['value' => '', 'label' => '']]],
                'malicious' => ['x' => 'y'],
            ],
        ])->assertRedirect();

        $sections = $home->fresh()->sections;
        $this->assertSame('Comunicação que *inspira*', $sections['hero']['title']);
        $this->assertNull($sections['hero']['primary_url']);
        $this->assertSame($image->id, $sections['hero']['image']);
        $this->assertSame('<p>Olá</p>', $sections['about']['text']);
        $this->assertCount(1, $sections['about']['stats']);
        $this->assertArrayNotHasKey('malicious', $sections);

        $this->get('/')->assertSee('Comunicação que <span class="accent-serif">inspira</span>', false);
    }

    public function test_custom_pages_cannot_use_reserved_slugs(): void
    {
        $this->actingAs($this->editor)->post('/admin/pages', ['title' => 'Blog', 'slug' => 'blog'])->assertSessionHasErrors('slug');
        $this->actingAs($this->editor)->post('/admin/pages', ['title' => 'Termos e condições', 'show_in_menu' => 1, 'is_published' => 1])->assertRedirect();

        $this->get('/termos-e-condicoes')->assertOk();
        $this->get('/')->assertSee('Termos e condições');
    }

    public function test_settings_update_is_reflected_publicly(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/admin/settings', [
            'company_name' => 'Tindziva Comunicação & Ideias',
            'contact_email' => 'geral@tindziva.co.mz',
            'contact_phone' => '+258 84 000 0000',
            'whatsapp_enabled' => 1,
            'whatsapp_number' => '+258 84 000 0000',
            'social_instagram' => 'https://instagram.com/tindziva',
            'map_embed_url' => 'https://evil.example/embed',
        ])->assertSessionHasErrors('map_embed_url');

        $this->actingAs($admin)->put('/admin/settings', [
            'company_name' => 'Tindziva Comunicação & Ideias',
            'contact_email' => 'geral@tindziva.co.mz',
            'whatsapp_enabled' => 1,
            'whatsapp_number' => '+258 84 000 0000',
            'social_instagram' => 'https://instagram.com/tindziva',
        ])->assertSessionHas('success');

        $this->get('/contactos')
            ->assertSee('geral@tindziva.co.mz')
            ->assertSee('https://wa.me/258840000000', false)
            ->assertSee('https://instagram.com/tindziva', false);
    }

    public function test_media_upload_optimises_images_and_rejects_unsafe_files(): void
    {
        Storage::fake('public');

        $this->actingAs($this->editor)->postJson('/admin/media', [
            'files' => [UploadedFile::fake()->image('Foto Evento.jpg', 3600, 2400)],
            'alt_text' => 'Convidados no evento',
        ])->assertCreated()->assertJsonPath('data.0.alt', 'Convidados no evento');

        $media = Media::where('original_name', 'Foto Evento.jpg')->sole();
        $this->assertSame(MediaService::MAX_IMAGE_DIMENSION, max($media->width, $media->height));
        $this->assertNotNull($media->thumbnail_path);
        Storage::disk('public')->assertExists([$media->path, $media->thumbnail_path]);

        $this->actingAs($this->editor)->postJson('/admin/media', [
            'files' => [UploadedFile::fake()->create('ataque.svg', 10, 'image/svg+xml')],
        ])->assertUnprocessable();

        $this->actingAs($this->editor)->postJson('/admin/media', [
            'files' => [UploadedFile::fake()->create('script.php', 10, 'text/x-php')],
        ])->assertUnprocessable();

        $this->actingAs($this->editor)->getJson('/admin/media?q=Foto')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_quote_status_and_notes_can_be_updated(): void
    {
        $commercial = User::factory()->commercial()->create();
        $quote = QuoteRequest::factory()->create();

        $this->actingAs($commercial)->put("/admin/quote-requests/{$quote->id}", [
            'status' => LeadStatus::ProposalSent->value,
            'internal_notes' => 'Proposta enviada por e-mail.',
            'assigned_to' => $commercial->id,
        ])->assertRedirect();

        $quote->refresh();
        $this->assertSame(LeadStatus::ProposalSent, $quote->status);
        $this->assertSame($commercial->id, $quote->assigned_to);

        $this->actingAs($commercial)->get('/admin/quote-requests?status=proposal_sent')->assertSee($quote->name);
        $this->actingAs($commercial)->get('/admin/quote-requests/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_new_users_receive_an_invitation_instead_of_a_known_password(): void
    {
        Notification::fake();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post('/admin/users', [
            'name' => 'Nova Autora',
            'email' => 'Autora@Tindziva.test',
            'role' => 'author',
            'is_active' => 1,
        ])->assertRedirect('/admin/users');

        $user = User::where('email', 'autora@tindziva.test')->sole();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_taxonomies_can_be_managed(): void
    {
        $this->actingAs($this->editor)->post('/admin/categories/projects', ['name' => 'Fotografia'])->assertRedirect();
        $category = ProjectCategory::where('slug', 'fotografia')->sole();

        $this->actingAs($this->editor)->put("/admin/categories/projects/{$category->id}", ['name' => 'Fotografia e vídeo'])->assertRedirect();
        $this->assertSame('Fotografia e vídeo', $category->fresh()->name);

        $this->actingAs($this->editor)->get('/admin/categories/invalido')->assertNotFound();
    }

    public function test_admin_changes_are_written_to_the_activity_log(): void
    {
        $this->actingAs($this->editor)->post('/admin/clients', ['name' => 'Novo Parceiro', 'type' => 'partner', 'is_visible' => 1]);

        $log = ActivityLog::where('action', 'created')->latest('id')->first();
        $this->assertSame($this->editor->id, $log->user_id);
        $this->assertStringContainsString('Novo Parceiro', $log->description);
    }

    public function test_editing_a_post_keeps_its_inactive_author(): void
    {
        $formerAuthor = User::factory()->author()->inactive()->create(['name' => 'Autora Antiga']);
        $post = Post::factory()->for($formerAuthor, 'author')->create();

        $this->actingAs($this->editor)->get("/admin/posts/{$post->id}/edit")
            ->assertSee('<option value="'.$formerAuthor->id.'" selected', false);

        $this->actingAs($this->editor)->put("/admin/posts/{$post->id}", [
            'title' => 'Título revisto',
            'status' => PostStatus::Published->value,
            'user_id' => $formerAuthor->id,
        ])->assertSessionHasNoErrors();

        $this->assertSame($formerAuthor->id, $post->fresh()->user_id);
    }

    public function test_client_segment_and_services_provided_are_saved(): void
    {
        $this->actingAs($this->editor)->post('/admin/clients', [
            'name' => 'Cliente Novo',
            'type' => 'client',
            'segment' => 'civil_society',
            'services_provided' => 'Assessoria de imprensa, , Produção de eventos',
            'is_visible' => 1,
        ])->assertRedirect();

        $client = Client::where('slug', 'cliente-novo')->sole();
        $this->assertSame(ClientSegment::CivilSociety, $client->segment);
        $this->assertSame(['Assessoria de imprensa', 'Produção de eventos'], $client->services_provided);
    }
}
