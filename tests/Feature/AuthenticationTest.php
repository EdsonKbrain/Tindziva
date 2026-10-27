<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/posts')->assertRedirect(route('login'));
    }

    public function test_users_can_log_in_and_login_is_recorded(): void
    {
        $user = User::factory()->editor()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertTrue(ActivityLog::where('action', 'login')->where('user_id', $user->id)->exists());
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_users_are_logged_out_on_next_request(): void
    {
        $user = User::factory()->editor()->create();
        $this->actingAs($user)->get('/admin')->assertOk();

        $user->update(['is_active' => false]);

        $this->actingAs($user->fresh())->get('/admin')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/admin/register')->assertNotFound();
        $this->get('/register')->assertNotFound();
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_login_is_rate_limited(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'errada']);
        }

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'errada'])->assertTooManyRequests();
    }
}
