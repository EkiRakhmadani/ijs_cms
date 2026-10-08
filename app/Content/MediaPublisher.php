<?php

namespace App\Content;

use App\Models\MediaAsset;
use RuntimeException;

/**
 * Copies uploaded files into the frontend's public directory.
 *
 * Published names are content-addressed, which buys two things: the frontend
 * can cache them forever, and replacing a picture can never serve the old one
 * from a stale cache — a different picture is simply a different filename.
 *
 * The flip side is that superseded files linger, so pruning is offered
 * separately rather than done silently: deleting is the one step here that
 * cannot be undone by running the command again.
 */
class MediaPublisher
{
    /** @var list<string> */
    private array $copied = [];

    private int $skipped = 0;

    /** @var list<string> */
    private array $missing = [];

    public function __construct(
        private readonly string $frontendPath,
        private readonly string $mediaDir,
        private readonly int $graceMinutes = 60,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (string) config('content.frontend_path'),
            (string) config('content.media_dir'),
            (int) config('content.storage_prune_grace_minutes', 60),
        );
    }

    public function directory(): string
    {
        return $this->frontendPath.'/'.$this->mediaDir;
    }

    /**
     * @return array{copied: list<string>, skipped: int, missing: list<string>}
     */
    public function publish(): array
    {
        $this->copied = [];
        $this->skipped = 0;
        $this->missing = [];

        $directory = $this->directory();

        if (! is_dir($directory) && ! mkdir($directory, 0o755, true) && ! is_dir($directory)) {
            throw new RuntimeException("Could not create media directory: {$directory}");
        }

        foreach (MediaAsset::query()->cursor() as $asset) {
            if (! $asset->exists()) {
                $this->missing[] = $asset->path ?? "media asset #{$asset->getKey()}";

                continue;
            }

            $target = $directory.'/'.$asset->publishedName();

            // The name is the content hash, so a file already there is already
            // correct — no need to read or rewrite it.
            if (is_file($target)) {
                $this->skipped++;

                continue;
            }

            if (! copy($asset->disk()->path($asset->path), $target)) {
                throw new RuntimeException("Could not copy media: {$asset->path}");
            }

            $this->copied[] = $asset->publishedName();
        }

        return [
            'copied' => $this->copied,
            'skipped' => $this->skipped,
            'missing' => $this->missing,
        ];
    }

    /**
     * Published files no longer backed by a media asset.
     *
     * @return list<string>
     */
    public function orphans(): array
    {
        $directory = $this->directory();

        if (! is_dir($directory)) {
            return [];
        }

        $expected = MediaAsset::query()
            ->get()
            ->map(fn (MediaAsset $asset): string => $asset->publishedName())
            ->flip();

        $orphans = [];

        foreach (scandir($directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..' || $entry === '.gitkeep') {
                continue;
            }

            if (! is_file($directory.'/'.$entry)) {
                continue;
            }

            if (! $expected->has($entry)) {
                $orphans[] = $entry;
            }
        }

        return $orphans;
    }

    /**
     * @return list<string> The files removed.
     */
    public function prune(): array
    {
        $removed = [];

        foreach ($this->orphans() as $orphan) {
            if (unlink($this->directory().'/'.$orphan)) {
                $removed[] = $orphan;
            }
        }

        return $removed;
    }

    /**
     * Uploads on this server that no media asset row points at.
     *
     * Replacing a file in the panel leaves the previous one behind, and that
     * is what accumulates here. Deleting a row leaves one too.
     *
     * Recently-touched files are deliberately left alone: Filament writes an
     * upload the moment it is chosen, before the form is submitted, so a file
     * that looks orphaned may simply belong to a form somebody still has open.
     *
     * @return list<string>
     */
    public function storageOrphans(): array
    {
        $disk = MediaAsset::query()->make()->disk();

        if (! $disk->exists('media')) {
            return [];
        }

        $known = MediaAsset::query()->pluck('path')->flip();
        $cutoff = now()->subMinutes($this->graceMinutes)->getTimestamp();

        $orphans = [];

        foreach ($disk->files('media') as $file) {
            if ($known->has($file)) {
                continue;
            }

            if ($disk->lastModified($file) > $cutoff) {
                continue;
            }

            $orphans[] = $file;
        }

        return $orphans;
    }

    /**
     * @return list<string> The files removed.
     */
    public function pruneStorage(): array
    {
        $disk = MediaAsset::query()->make()->disk();
        $removed = [];

        foreach ($this->storageOrphans() as $orphan) {
            if ($disk->delete($orphan)) {
                $removed[] = $orphan;
            }
        }

        return $removed;
    }
}
