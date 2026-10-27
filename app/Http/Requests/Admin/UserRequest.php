<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return $user ? $this->user()->can('update', $user) : $this->user()->can('create', User::class);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::enum(Role::class)],
            'permissions' => ['array'],
            'permissions.*' => [Rule::enum(Permission::class)],
            'job_title' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $role = Role::tryFrom((string) $this->input('role'));
            /** @var User|null $target */
            $target = $this->route('user');

            if ($role && ! $this->user()->can('assignRole', [User::class, $role])) {
                $validator->errors()->add('role', 'Não tem permissão para atribuir este perfil.');
            }

            if ($target && $target->is($this->user()) && ($role !== $target->role || ! $this->boolean('is_active'))) {
                $validator->errors()->add('role', 'Não pode alterar o seu próprio perfil nem desactivar a sua conta.');
            }
        }];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->safe()->except(['password', 'password_confirmation', 'permissions']);
        $role = Role::from($data['role']);

        $data['permissions'] = $role->hasCustomizablePermissions() && $this->has('customize_permissions')
            ? array_values((array) $this->input('permissions', []))
            : null;

        return $data;
    }
}
