<?php

namespace App\Models;

use Database\Factories\PageSeoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    /** @use HasFactory<PageSeoFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'path',
        'title_id',
        'title_en',
        'description_key',
        'description_id',
        'description_en',
        'position',
    ];

    /**
     * @param  Builder<PageSeo>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /**
     * Null means the bare site title, with no page name in front of it.
     *
     * @return array{id: ?string, en: ?string}|null
     */
    public function title(): ?array
    {
        if ($this->title_id === null && $this->title_en === null) {
            return null;
        }

        return ['id' => $this->title_id, 'en' => $this->title_en];
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
