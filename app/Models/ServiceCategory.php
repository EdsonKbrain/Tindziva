<?php

namespace App\Models;

use App\Models\Concerns\FlushesPublicCache;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Database\Factories\ServiceCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'sort_order'])]
class ServiceCategory extends Model
{
    /** @use HasFactory<ServiceCategoryFactory> */
    use FlushesPublicCache, HasFactory, HasSlug, LogsActivity;

    public static function activityLabel(): string
    {
        return 'categoria de serviço';
    }

    /**
     * @return HasMany<Service, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
