<?php

namespace App\Console\Commands;

use App\Content\ContentWriter;
use App\Content\Contracts\Exporter;
use App\Content\MediaPublisher;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Publishes CMS content into the frontend as static JSON.
 *
 * The frontend prerenders every page at build time and must not call this API
 * at runtime, so the database is the source of truth and this command is what
 * materialises it. Run it after editing content, then rebuild the frontend.
 */
class ContentExport extends Command
{
    protected $signature = 'content:export
                            {--dry-run : Show what would be written without writing it}
                            {--prune : Delete published media no longer backed by an asset}';

    protected $description = 'Publish CMS content into the frontend as static JSON';

    public function handle(): int
    {
        $writer = ContentWriter::fromConfig();

        try {
            $writer->guard();
        } catch (RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if (($media = $this->publishMedia()) !== self::SUCCESS) {
            return $media;
        }

        /** @var list<class-string<Exporter>> $exporters */
        $exporters = config('content.exporters', []);

        if ($exporters === []) {
            $this->components->warn('No exporters configured.');

            return self::SUCCESS;
        }

        $this->components->info(
            ($this->option('dry-run') ? 'Previewing' : 'Publishing').' to '.$writer->generatedPath()
        );

        $changed = 0;

        foreach ($exporters as $class) {
            /** @var Exporter $exporter */
            $exporter = app($class);
            $filename = $exporter->filename();

            try {
                $data = $exporter->data();
            } catch (\Throwable $e) {
                $this->components->error("{$filename}: {$e->getMessage()}");

                return self::FAILURE;
            }

            if ($this->option('dry-run')) {
                $bytes = strlen($writer->encode($data));
                $this->components->twoColumnDetail(
                    $filename,
                    sprintf('%d entries, %d bytes', count($data), $bytes)
                );

                continue;
            }

            try {
                $wrote = $writer->write($filename, $data);
            } catch (RuntimeException $e) {
                $this->components->error("{$filename}: {$e->getMessage()}");

                return self::FAILURE;
            }

            $changed += $wrote ? 1 : 0;

            $this->components->twoColumnDetail(
                $filename,
                $wrote ? '<fg=green>written</>' : '<fg=gray>unchanged</>'
            );
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->components->warn('Dry run — nothing was written.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->components->info(
            $changed === 0
                ? 'Already up to date.'
                : "{$changed} file(s) updated. Rebuild the frontend to publish them."
        );

        return self::SUCCESS;
    }

    /**
     * Copy uploaded files across before the JSON that points at them, so the
     * frontend is never left referencing a picture that is not there yet.
     */
    private function publishMedia(): int
    {
        $publisher = MediaPublisher::fromConfig();

        if ($this->option('dry-run')) {
            $orphans = count($publisher->orphans());
            $this->components->twoColumnDetail(
                'media',
                $orphans === 0 ? '<fg=gray>no orphans</>' : "{$orphans} orphan(s) would be left"
            );

            return self::SUCCESS;
        }

        try {
            $result = $publisher->publish();
        } catch (RuntimeException $e) {
            $this->components->error("media: {$e->getMessage()}");

            return self::FAILURE;
        }

        $copied = count($result['copied']);

        $this->components->twoColumnDetail(
            'media',
            $copied === 0
                ? sprintf('<fg=gray>%d already published</>', $result['skipped'])
                : sprintf('<fg=green>%d copied</>, %d already published', $copied, $result['skipped'])
        );

        // An asset row whose file has gone is a broken picture on the site,
        // so say so rather than publishing JSON that points at nothing.
        foreach ($result['missing'] as $missing) {
            $this->components->warn("Media file missing on disk, skipped: {$missing}");
        }

        if ($this->option('prune')) {
            $removed = count($publisher->prune());
            $this->components->twoColumnDetail(
                'prune frontend',
                $removed === 0 ? '<fg=gray>nothing to remove</>' : "<fg=yellow>{$removed} removed</>"
            );

            $removedUploads = count($publisher->pruneStorage());
            $this->components->twoColumnDetail(
                'prune uploads',
                $removedUploads === 0 ? '<fg=gray>nothing to remove</>' : "<fg=yellow>{$removedUploads} removed</>"
            );

            return self::SUCCESS;
        }

        if (($orphans = count($publisher->orphans())) > 0) {
            $this->components->warn("{$orphans} published file(s) no longer used. Run with --prune to remove them.");
        }

        if (($abandoned = count($publisher->storageOrphans())) > 0) {
            $this->components->warn("{$abandoned} uploaded file(s) no longer referenced. Run with --prune to remove them.");
        }

        return self::SUCCESS;
    }
}
