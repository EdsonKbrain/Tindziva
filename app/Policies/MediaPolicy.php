<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ManageMedia);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::ManageMedia);
    }

    /**
     * Authors may only change their own uploads; editors and administrators may change any file.
     */
    public function update(User $user, Media $media): bool
    {
        if (! $user->hasPermission(Permission::ManageMedia)) {
            return false;
        }

        return $media->uploaded_by === $user->id
            || $user->hasPermission(Permission::ManagePages)
            || $user->hasPermission(Permission::ManageAllPosts);
    }

    public function delete(User $user, Media $media): bool
    {
        return $this->update($user, $media);
    }
}
