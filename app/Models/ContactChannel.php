<?php

namespace App\Models;

use Database\Factories\ContactChannelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactChannel extends Model
{
    /** @use HasFactory<ContactChannelFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'href',
        'label_id',
        'label_en',
        'postal',
        'position',
    ];

    /**
     * @param  Builder<ContactChannel>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /**
     * The label in the shape the frontend expects: a plain string when it
     * reads the same either way, an { id, en } pair only when it genuinely
     * differs. Writing the shared ones once is what stops the two languages
     * drifting apart on text that was never meant to differ.
     *
     * @return string|array{id: string, en: string}
     */
    public function label(): string|array
    {
        if ($this->label_en === null || $this->label_en === $this->label_id) {
            return $this->label_id;
        }

        return ['id' => $this->label_id, 'en' => $this->label_en];
    }

    protected function casts(): array
    {
        return [
            'postal' => 'array',
            'position' => 'integer',
        ];
    }
}
