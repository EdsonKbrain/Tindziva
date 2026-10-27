<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'company', 'email', 'phone', 'subject', 'message', 'status', 'internal_notes', 'ip_address', 'user_agent', 'handled_by', 'replied_at'])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory, LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'replied_at' => 'datetime',
        ];
    }

    public static function activityLabel(): string
    {
        return 'mensagem de contacto';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * @param  Builder<ContactMessage>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (filled($term)) {
            $query->where(fn (Builder $query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('subject', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%"));
        }
    }

    /**
     * @param  Builder<ContactMessage>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('status', LeadStatus::open());
    }
}
