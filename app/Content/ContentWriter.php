<?php

namespace App\Content;

use RuntimeException;

/**
 * Writes published content into the frontend checkout.
 *
 * Everything here exists to make publishing safe to run repeatedly: the target
 * is checked before anything is written, the JSON is byte-stable so git only
 * shows real content changes, and each file lands via rename so a crashed run
 * cannot leave the frontend importing half a file.
 */
class ContentWriter
{
    private const ENCODE_FLAGS = JSON_PRETTY_PRINT
        | JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_THROW_ON_ERROR;

    public function __construct(
        private readonly string $frontendPath,
        private readonly string $generatedDir,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (string) config('content.frontend_path'),
            (string) config('content.generated_dir'),
        );
    }

    /**
     * Fail before writing if the target is not actually the frontend.
     *
     * A mistyped FRONTEND_PATH would otherwise scatter JSON into whatever
     * directory it happens to name.
     */
    public function guard(): void
    {
        if (! is_dir($this->frontendPath)) {
            throw new RuntimeException("Frontend path does not exist: {$this->frontendPath}");
        }

        foreach (['next.config.mjs', 'src/data'] as $marker) {
            if (! file_exists($this->frontendPath.'/'.$marker)) {
                throw new RuntimeException(
                    "{$this->frontendPath} does not look like the frontend (missing {$marker}). ".
                    'Refusing to write. Check FRONTEND_PATH.'
                );
            }
        }
    }

    public function generatedPath(string $filename = ''): string
    {
        return rtrim($this->frontendPath.'/'.$this->generatedDir.'/'.$filename, '/');
    }

    /**
     * Serialise exactly as write() would, without touching the filesystem.
     *
     * @param  array<array-key, mixed>  $data
     */
    public function encode(array $data): string
    {
        return json_encode($data, self::ENCODE_FLAGS)."\n";
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return bool Whether the file's contents actually changed.
     */
    public function write(string $filename, array $data): bool
    {
        $target = $this->generatedPath($filename);
        $json = $this->encode($data);

        $directory = dirname($target);

        if (! is_dir($directory) && ! mkdir($directory, 0o755, true) && ! is_dir($directory)) {
            throw new RuntimeException("Could not create directory: {$directory}");
        }

        if (is_file($target) && file_get_contents($target) === $json) {
            return false;
        }

        // Same directory as the target so the rename stays on one filesystem
        // and is therefore atomic.
        $temp = $target.'.tmp'.getmypid();

        if (file_put_contents($temp, $json) === false) {
            throw new RuntimeException("Could not write: {$temp}");
        }

        if (! rename($temp, $target)) {
            @unlink($temp);

            throw new RuntimeException("Could not move into place: {$target}");
        }

        return true;
    }
}
