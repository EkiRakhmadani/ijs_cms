<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();

            // Where the original lives on this server, on the `public` disk.
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime')->nullable();

            // Recorded so the frontend can set explicit dimensions and avoid
            // shifting the page as pictures load.
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('bytes')->nullable();

            /*
             * Content hash. The published filename is derived from it, which
             * makes every published asset immutable: a different picture is a
             * different name, so the frontend can cache them forever and a
             * replaced image can never be served stale.
             */
            $table->string('sha256', 64)->nullable()->index();

            // Alternative text is read out to visitors, so it is bilingual
            // like every other visitor-facing string on the site.
            $table->text('alt_id')->nullable();
            $table->text('alt_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
