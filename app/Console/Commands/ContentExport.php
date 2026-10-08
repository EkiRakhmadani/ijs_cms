<?php

namespace App\Console\Commands;

use App\Content\ContentWriter;
use App\Content\Contracts\Exporter;
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
                            {--dry-run : Show what would be written without writing it}';

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
}
