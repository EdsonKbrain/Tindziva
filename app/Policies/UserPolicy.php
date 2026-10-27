<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ManageUsers);
    }

    /**
     * Only super administrators may manage other super administrators.
     */
    public function update(User $user, User $model): bool
    {
        if (! $user->hasPermission(Permission::ManageUsers)) {
            return false;
        }

        return $model->role !== Role::SuperAdmin || $user->isSuperAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isNot($model) && $this->update($user, $model);
    }

    public function assignRole(User $user, Role $role): bool
    {
        return $user->hasPermission(Permission::ManageUsers) && ($role !== Role::SuperAdmin || $user->isSuperAdmin());
    }
}
