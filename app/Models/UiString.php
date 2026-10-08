<?php

namespace App\Models;

use Database\Factories\UiStringFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UiString extends Model
{
    /** @use HasFactory<UiStringFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'group',
        'value_id',
        'value_en',
        'position',
    ];

    /**
     * @param  Builder<UiString>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('key');
    }

    /**
     * The group is the key's first segment, always — derived rather than
     * typed, so the two can never disagree.
     *
     * Done as a mutator rather than a saving hook on purpose: seeders run
     * under WithoutModelEvents, and a required column that depends on an
     * event is a column that is null exactly when it matters most.
     *
     * @return Attribute<string, array{key: string, group: string}>
     */
    protected function key(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): array => [
                'key' => $value,
                'group' => Str::before($value, '.'),
            ],
        );
    }
}
