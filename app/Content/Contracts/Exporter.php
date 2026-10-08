<?php

namespace App\Content\Contracts;

interface Exporter
{
    /**
     * The file this exporter owns, relative to the generated directory.
     */
    public function filename(): string;

    /**
     * The content to serialise.
     *
     * Must be deterministic: the same database state has to produce the same
     * array in the same order every time, or publishing churns the frontend's
     * git history with diffs that mean nothing.
     *
     * @return array<array-key, mixed>
     */
    public function data(): array;
}
