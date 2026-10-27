<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManagePages);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ManagePages);
    }

    public function update(User $user, Page $page): bool
    {
        return $user->hasPermission(Permission::ManagePages);
    }

    /**
     * System pages back fixed public routes and cannot be removed.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->hasPermission(Permission::ManagePages) && ! $page->is_system;
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->hasPermission(Permission::ManagePages);
    }
}
