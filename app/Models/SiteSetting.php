<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * The site's own identity — one row, because the site has one.
 */
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'url',
        'title',
        'name',
        'legal_name',
        'short_name',
        'locale_id',
        'locale_en',
    ];

    /**
     * The single row, created with Laravel's defaults if it is not there yet
     * so that reading never returns null.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'url' => 'https://ptijs.com',
            'title' => 'IJS - Internusa Jayaabadi Sentosa',
            'name' => 'Internusa Jayaabadi Sentosa',
            'legal_name' => 'PT Internusa Jayaabadi Sentosa',
            'short_name' => 'IJS',
        ]);
    }
}
