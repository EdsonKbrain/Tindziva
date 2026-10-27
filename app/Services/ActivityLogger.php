<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    /**
     * Record an administrative action.
     *
     * @param  array<string, mixed>  $properties
     */
    public function log(string $action, string $description, ?Model $subject = null, array $properties = [], ?User $user = null): ActivityLog
    {
        $request = request();

        return ActivityLog::create([
            'user_id' => $user?->id ?? Auth::id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => Str::limit($description, 250),
            'properties' => $properties ?: null,
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 490),
            'created_at' => now(),
        ]);
    }
}
