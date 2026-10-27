<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateSuperAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_super_admin_with_a_strong_password(): void
    {
        $this->artisan('tindziva:create-admin', ['--name' => 'Direcção Tindziva', '--email' => 'Direccao@Tindziva.test'])
            ->expectsQuestion('Palavra-passe (mín. 10 caracteres, maiúsculas, minúsculas e números)', 'Segura2026Tz')
            ->expectsQuestion('Confirmar palavra-passe', 'Segura2026Tz')
            ->assertSuccessful();

        $user = User::where('email', 'direccao@tindziva.test')->sole();
        $this->assertSame(Role::SuperAdmin, $user->role);
        $this->assertTrue(Hash::check('Segura2026Tz', $user->password));
    }

    public function test_it_rejects_weak_passwords(): void
    {
        $this->artisan('tindziva:create-admin', ['--name' => 'Teste', '--email' => 'teste@tindziva.test'])
            ->expectsQuestion('Palavra-passe (mín. 10 caracteres, maiúsculas, minúsculas e números)', 'password')
            ->expectsQuestion('Confirmar palavra-passe', 'password')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_it_reads_the_password_from_the_environment_without_prompts(): void
    {
        putenv('TINDZIVA_ADMIN_PASSWORD=Segura2026Tz');

        try {
            $this->artisan('tindziva:create-admin', ['--name' => 'Direcção', '--email' => 'admin@tindziva.test', '--no-interaction' => true])
                ->assertSuccessful();
        } finally {
            putenv('TINDZIVA_ADMIN_PASSWORD');
        }

        $this->assertTrue(Hash::check('Segura2026Tz', User::where('email', 'admin@tindziva.test')->sole()->password));
    }
}
