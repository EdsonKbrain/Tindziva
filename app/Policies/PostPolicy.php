<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::WritePosts) || $user->hasPermission(Permission::ManageAllPosts);
    }

    public function view(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::WritePosts) || $user->hasPermission(Permission::ManageAllPosts);
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->hasPermission(Permission::ManageAllPosts)) {
            return true;
        }

        return $user->hasPermission(Permission::WritePosts) && $post->user_id === $user->id;
    }

    /**
     * Publishing or scheduling makes the post public, which requires editorial approval.
     */
    public function publish(User $user, ?Post $post = null): bool
    {
        return $user->hasPermission(Permission::PublishPosts) && ($post === null || $this->update($user, $post));
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function restore(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasPermission(Permission::ManageAllPosts);
    }
}
