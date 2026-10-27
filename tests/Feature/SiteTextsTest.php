<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\Support\SiteTexts;
use Database\Seeders\PageSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTextsTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, PageSeeder::class, ServiceSeeder::class]);
        $this->editor = User::factory()->editor()->create();
    }

    /**
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    private function texts(array $changes = []): array
    {
        $texts = [];

        foreach (SiteTexts::fields() as $key => $field) {
            $texts[$key] = ($field['type'] ?? 'text') === 'toggle' ? (int) $field['default'] : null;
        }

        return ['texts' => array_replace($texts, $changes)];
    }

    public function test_texts_screen_lists_every_editable_text(): void
    {
        $this->actingAs($this->editor)->get('/admin/texts')
            ->assertOk()
            ->assertSee('Menu: Início')
            ->assertSee('Proposta: opções de orçamento')
            ->assertSee('placeholder="Pedir proposta"', false);
    }

    public function test_only_users_who_manage_pages_can_edit_texts(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->get('/admin/texts')->assertForbidden();
        $this->actingAs($author)->put('/admin/texts', $this->texts(['nav_home' => 'Hack']))->assertForbidden();
    }

    public function test_menu_labels_visibility_and_header_button_are_editable(): void
    {
        $this->actingAs($this->editor)->put('/admin/texts', $this->texts([
            'nav_portfolio' => 'Trabalhos',
            'nav_show_blog' => 0,
            'header_cta_label' => 'Fale connosco',
            'header_cta_url' => '/contactos',
        ]))->assertRedirect();

        $this->get('/')
            ->assertSee('Trabalhos')
            ->assertSee('Fale connosco')
            ->assertDontSee('href="'.route('blog.index').'"', false);
    }

    public function test_empty_fields_fall_back_to_defaults(): void
    {
        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['form_quote_submit' => 'Pedir agora']));
        $this->get('/contactos')->assertSee('Pedir agora');

        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['form_quote_submit' => '']));
        $this->get('/contactos')->assertSee('Enviar pedido');
    }

    public function test_texts_are_stored_as_plain_text_and_links_are_validated(): void
    {
        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['header_cta_url' => 'javascript:alert(1)']))
            ->assertSessionHasErrors('texts.header_cta_url');

        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['cta_title' => '<script>x</script>Olá']));
        $this->assertSame('xOlá', SiteTexts::get('cta_title'));
    }

    public function test_budget_options_drive_the_quote_form_and_its_validation(): void
    {
        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['form_budget_options' => "Pequeno\nGrande"]));

        $this->get('/contactos')->assertSee('<option value="Grande"', false)->assertDontSee('Até 50 000 MT');

        $quote = ['name' => 'Ana', 'email' => 'ana@example.com', 'phone' => '840000000', 'message' => 'Mensagem de teste suficiente.'];
        $this->post('/propostas', [...$quote, 'budget' => 'Até 50 000 MT'])->assertSessionHasErrors('budget', errorBag: 'quote');
        $this->post('/propostas', [...$quote, 'budget' => 'Grande'])->assertSessionHasNoErrors();

        $this->actingAs($this->editor)->put('/admin/texts', $this->texts(['form_budget_options' => '']));
        $this->get('/contactos')->assertDontSee('name="budget"', false);
    }

    public function test_service_and_post_labels_use_placeholders(): void
    {
        $this->actingAs($this->editor)->put('/admin/texts', $this->texts([
            'service_quote_title' => 'Precisa de :service?',
            'reading_time' => 'Leitura: :minutes min',
        ]));

        $service = Service::where('slug', 'marketing-digital')->sole();
        $post = Post::factory()->create();

        $this->get($service->url())->assertSee('Precisa de marketing digital?');
        $this->get($post->url())->assertSee('Leitura: '.$post->reading_time.' min');
    }

    public function test_page_sections_can_be_hidden_and_eyebrows_edited(): void
    {
        $about = Page::forTemplate('about');
        $this->get('/sobre-nos')->assertSee('Inovação');

        $sections = $about->sections;
        $sections['purpose']['_visible'] = '0';
        $sections['intro']['eyebrow'] = 'Quem somos nós';

        $this->actingAs($this->editor)->put("/admin/pages/{$about->id}", [
            'title' => $about->title,
            'subtitle' => $about->subtitle,
            'sections' => $sections,
        ])->assertRedirect();

        $this->get('/sobre-nos')->assertDontSee('Inovação')->assertSee('Quem somos nós');
    }
}
