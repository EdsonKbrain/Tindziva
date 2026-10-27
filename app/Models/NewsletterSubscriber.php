<?php

namespace App\Models;

use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'name', 'status', 'token', 'ip_address', 'unsubscribed_at'])]
class NewsletterSubscriber extends Model
{
    /** @use HasFactory<NewsletterSubscriberFactory> */
    use HasFactory;

    public const STATUS_SUBSCRIBED = 'subscribed';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (NewsletterSubscriber $subscriber): void {
            $subscriber->token ??= Str::random(48);
        });
    }

    /**
     * @param  Builder<NewsletterSubscriber>  $query
     */
    public function scopeSubscribed(Builder $query): void
    {
        $query->where('status', self::STATUS_SUBSCRIBED);
    }
}
