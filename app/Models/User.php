<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Concerns\LogsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'permissions', 'job_title', 'bio', 'avatar_path', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, LogsActivity, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public static function activityLabel(): string
    {
        return 'utilizador';
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SuperAdmin;
    }

    /**
     * The permissions this user effectively holds.
     *
     * @return list<Permission>
     */
    public function effectivePermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::cases();
        }

        if ($this->role->hasCustomizablePermissions() && is_array($this->permissions)) {
            return array_values(array_filter(array_map(
                fn (string $value): ?Permission => Permission::tryFrom($value),
                $this->permissions,
            )));
        }

        return $this->role->defaultPermissions();
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->is_active && in_array($permission, $this->effectivePermissions(), true);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk(config('filesystems.media_disk'))->url($this->avatar_path) : null;
    }

    public function initials(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
