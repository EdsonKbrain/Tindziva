<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('tindziva:create-admin {--name= : Nome completo} {--email= : Endereço de e-mail}')]
#[Description('Create a super administrator account; the password is asked interactively or read from TINDZIVA_ADMIN_PASSWORD')]
class CreateSuperAdmin extends Command
{
    public function handle(): int
    {
        // Hosting without proc_open cannot run interactive prompts, so the deploy script asks for the
        // password in bash and passes it through the environment (never as a visible argument).
        $environmentPassword = getenv('TINDZIVA_ADMIN_PASSWORD') ?: null;

        $name = $this->option('name') ?: text('Nome completo', required: true);
        $email = strtolower((string) ($this->option('email') ?: text('E-mail', required: true)));
        $password = $environmentPassword ?? (string) password(
            'Palavra-passe (mín. 10 caracteres, maiúsculas, minúsculas e números)',
            required: true,
        );
        $confirmation = $environmentPassword ?? (string) password('Confirmar palavra-passe', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
            [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:190', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(['name' => $name, 'email' => $email, 'role' => Role::SuperAdmin, 'is_active' => true]);
        $user->password = Hash::make($password);
        $user->email_verified_at = now();
        $user->save();

        $this->components->info("Superadministrador {$user->email} criado. Aceda a ".route('login'));

        return self::SUCCESS;
    }
}
