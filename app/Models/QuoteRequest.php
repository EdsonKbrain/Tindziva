<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Models\Concerns\LogsActivity;
use Database\Factories\QuoteRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['service_id', 'service_name', 'name', 'company', 'email', 'phone', 'budget', 'deadline', 'message', 'status', 'internal_notes', 'source_url', 'ip_address', 'user_agent', 'assigned_to'])]
class QuoteRequest extends Model
{
    /** @use HasFactory<QuoteRequestFactory> */
    use HasFactory, LogsActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'deadline' => 'date',
        ];
    }

    public static function activityLabel(): string
    {
        return 'pedido de proposta';
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @param  Builder<QuoteRequest>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (filled($term)) {
            $query->where(fn (Builder $query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('service_name', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%"));
        }
    }

    /**
     * @param  Builder<QuoteRequest>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('status', LeadStatus::open());
    }
}
