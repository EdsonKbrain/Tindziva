<?php

namespace App\Models\Concerns;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Records create, update, delete and restore events performed by authenticated users.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => $model->recordActivity('created', 'Criou'));

        static::updated(function (Model $model): void {
            $changes = array_keys($model->getChanges());

            if (array_diff($changes, ['updated_at', 'deleted_at', 'remember_token', 'last_login_at']) !== []) {
                $model->recordActivity('updated', 'Actualizou', ['fields' => array_values(array_diff($changes, ['updated_at', 'password', 'remember_token']))]);
            }
        });

        static::deleted(fn (Model $model) => $model->recordActivity('deleted', 'Eliminou'));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->recordActivity('restored', 'Restaurou'));
        }
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function recordActivity(string $action, string $verb, array $properties = []): void
    {
        if (! Auth::check()) {
            return;
        }

        $label = $this->getAttribute('title') ?? $this->getAttribute('name') ?? $this->getAttribute('original_name') ?? "#{$this->getKey()}";

        app(ActivityLogger::class)->log(
            $action,
            "{$verb} ".static::activityLabel()." «{$label}»",
            $this,
            $properties,
        );
    }

    public static function activityLabel(): string
    {
        return strtolower(class_basename(static::class));
    }
}
