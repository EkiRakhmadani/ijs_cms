<?php

namespace App\Models;

use Database\Factories\MediaAssetFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * A file uploaded through the CMS.
 *
 * The original stays on this server; publishing copies it into the frontend's
 * public directory under a content-addressed name, so the site serves it
 * same-origin — which is what the site's `img-src 'self'` CSP allows, with no
 * change to that policy.
 */
class MediaAsset extends Model
{
    /** @use HasFactory<MediaAssetFactory> */
    use HasFactory;

    protected $fillable = [
        'path',
        'original_name',
        'mime',
        'width',
        'height',
        'bytes',
        'sha256',
        'alt_id',
        'alt_en',
    ];

    protected static function booted(): void
    {
        /*
         * Filament hands us a stored path and nothing else, so the file's own
         * facts are read back off disk here rather than asked of whoever
         * uploaded it. Re-read whenever the path changes, so replacing the
         * file cannot leave the old dimensions behind.
         */
        static::saving(function (self $asset): void {
            if ($asset->path && $asset->isDirty('path')) {
                $asset->readMetadataFromDisk();
            }
        });
    }

    /**
     * @return HasMany<SocialPost, $this>
     */
    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function disk(): Filesystem
    {
        return Storage::disk('public');
    }

    public function exists(): bool
    {
        return $this->path !== null && $this->disk()->exists($this->path);
    }

    public function extension(): string
    {
        return strtolower(pathinfo((string) $this->path, PATHINFO_EXTENSION)) ?: 'bin';
    }

    /**
     * The filename this asset is published under: content-addressed, so a
     * different picture is always a different URL.
     */
    public function publishedName(): string
    {
        $hash = $this->sha256 ?: hash('sha256', (string) $this->path);

        return substr($hash, 0, 12).'.'.$this->extension();
    }

    /**
     * The path the frontend references, relative to its own origin.
     */
    public function publishedUrl(): string
    {
        return '/media/'.$this->publishedName();
    }

    public function readMetadataFromDisk(): void
    {
        if (! $this->exists()) {
            return;
        }

        $absolute = $this->disk()->path($this->path);

        $this->sha256 = hash_file('sha256', $absolute) ?: null;
        $this->bytes = filesize($absolute) ?: null;
        $this->mime = $this->disk()->mimeType($this->path) ?: null;

        // Covers jpeg, png, gif and webp without pulling in an image library.
        $size = @getimagesize($absolute);

        if ($size !== false) {
            [$this->width, $this->height] = $size;
        }
    }

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'bytes' => 'integer',
        ];
    }
}
