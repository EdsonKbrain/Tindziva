<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Enums\Role;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{Role, string, int}>
     */
    public static function accessMatrix(): array
    {
        return [
            'author cannot manage services' => [Role::Author, '/admin/services', 403],
            'author cannot manage pages' => [Role::Author, '/admin/pages', 403],
            'author cannot edit site texts' => [Role::Author, '/admin/texts', 403],
            'editor can edit site texts' => [Role::Editor, '/admin/texts', 200],
            'author cannot read leads' => [Role::Author, '/admin/quote-requests', 403],
            'author cannot change settings' => [Role::Author, '/admin/settings', 403],
            'author can write posts' => [Role::Author, '/admin/posts', 200],
            'author can use media library' => [Role::Author, '/admin/media', 200],
            'commercial cannot open blog' => [Role::Commercial, '/admin/posts', 403],
            'commercial cannot open portfolio' => [Role::Commercial, '/admin/projects', 403],
            'commercial can manage quotes' => [Role::Commercial, '/admin/quote-requests', 200],
            'commercial can manage messages' => [Role::Commercial, '/admin/contact-messages', 200],
            'commercial can manage newsletter' => [Role::Commercial, '/admin/newsletter', 200],
            'editor can manage pages' => [Role::Editor, '/admin/pages', 200],
            'editor can manage portfolio' => [Role::Editor, '/admin/projects', 200],
            'editor cannot manage users' => [Role::Editor, '/admin/users', 403],
            'editor cannot read leads' => [Role::Editor, '/admin/contact-messages', 403],
            'admin cannot manage users by default' => [Role::Admin, '/admin/users', 403],
            'admin can change settings' => [Role::Admin, '/admin/settings', 200],
            'admin can view activity' => [Role::Admin, '/admin/activity', 200],
            'super admin can manage users' => [Role::SuperAdmin, '/admin/users', 200],
        ];
    }

    #[DataProvider('accessMatrix')]
    public function test_roles_are_enforced_on_the_server(Role $role, string $uri, int $status): void
    {
        $user = User::factory()->role($role)->create();

        $this->actingAs($user)->get($uri)->assertStatus($status);
    }

    public function test_forbidden_writes_are_rejected_not_just_hidden(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->post('/admin/services', ['name' => 'Serviço intruso'])->assertForbidden();
        $this->actingAs($author)->put('/admin/settings', ['company_name' => 'Hack'])->assertForbidden();
        $this->assertDatabaseMissing('services', ['name' => 'Serviço intruso']);
    }

    public function test_author_can_only_edit_own_posts(): void
    {
        $author = User::factory()->author()->create();
        $own = Post::factory()->draft()->for($author, 'author')->create();
        $foreign = Post::factory()->create();

        $this->actingAs($author)->get("/admin/posts/{$own->id}/edit")->assertOk();
        $this->actingAs($author)->get("/admin/posts/{$foreign->id}/edit")->assertForbidden();
        $this->actingAs($author)->put("/admin/posts/{$foreign->id}", ['title' => 'Alterado', 'status' => 'draft'])->assertForbidden();
        $this->actingAs($author)->delete("/admin/posts/{$foreign->id}")->assertForbidden();

        $this->actingAs($author)->get('/admin/posts')->assertSee($own->title)->assertDontSee($foreign->title);
    }

    public function test_author_cannot_publish_or_change_author(): void
    {
        $author = User::factory()->author()->create();
        $other = User::factory()->author()->create();

        $this->actingAs($author)->post('/admin/posts', [
            'title' => 'Tentativa de publicação',
            'status' => PostStatus::Published->value,
        ])->assertSessionHasErrors('status');

        $this->actingAs($author)->post('/admin/posts', [
            'title' => 'Tentativa de mudar autor',
            'status' => PostStatus::Draft->value,
            'user_id' => $other->id,
        ])->assertSessionHasErrors('user_id');

        $this->actingAs($author)->post('/admin/posts', [
            'title' => 'Rascunho do autor',
            'status' => PostStatus::Draft->value,
        ])->assertRedirect();

        $this->assertDatabaseHas('posts', ['title' => 'Rascunho do autor', 'user_id' => $author->id, 'status' => 'draft']);
    }

    public function test_system_pages_cannot_be_deleted(): void
    {
        $this->seed(PageSeeder::class);
        $editor = User::factory()->editor()->create();
        $home = Page::forTemplate('home');

        $this->actingAs($editor)->delete("/admin/pages/{$home->id}")->assertForbidden();
        $this->assertNotSoftDeleted($home);
    }

    public function test_admin_permissions_can_be_customised_per_user(): void
    {
        $admin = User::factory()->admin()->create(['permissions' => [Permission::ManageLeads->value]]);

        $this->actingAs($admin)->get('/admin/quote-requests')->assertOk();
        $this->actingAs($admin)->get('/admin/settings')->assertForbidden();
        $this->actingAs($admin)->get('/admin/projects')->assertForbidden();
    }

    public function test_only_super_admins_can_grant_super_admin_role(): void
    {
        $admin = User::factory()->admin()->create(['permissions' => [Permission::ManageUsers->value]]);
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Escalada', 'email' => 'escalada@example.com', 'role' => Role::SuperAdmin->value, 'is_active' => 1,
        ])->assertSessionHasErrors('role');

        $this->actingAs($admin)->get("/admin/users/{$superAdmin->id}/edit")->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'escalada@example.com']);
    }

    public function test_users_cannot_demote_or_delete_themselves(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->put("/admin/users/{$superAdmin->id}", [
            'name' => $superAdmin->name, 'email' => $superAdmin->email, 'role' => Role::Author->value, 'is_active' => 1,
        ])->assertSessionHasErrors('role');

        $this->actingAs($superAdmin)->delete("/admin/users/{$superAdmin->id}")->assertForbidden();
    }

    public function test_media_uploaded_by_others_cannot_be_deleted_by_authors(): void
    {
        $author = User::factory()->author()->create();
        $foreign = Media::factory()->create(['uploaded_by' => User::factory()->editor()->create()->id]);
        $own = Media::factory()->create(['uploaded_by' => $author->id]);

        $this->actingAs($author)->delete("/admin/media/{$foreign->id}")->assertForbidden();
        $this->actingAs($author)->delete("/admin/media/{$own->id}")->assertRedirect();
        $this->assertModelMissing($own);
    }
}
