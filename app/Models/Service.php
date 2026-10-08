<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'icon',
        'description_id',
        'description_en',
        'position',
    ];

    /**
     * The slug is what the public API and the frontend route on, not the id.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return HasMany<ServiceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ServiceItem::class)->orderBy('position');
    }

    /**
     * @param  Builder<Service>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /**
     * The path the Next.js frontend serves this service on.
     */
    public function path(): string
    {
        return '/'.$this->slug;
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
