<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seos', function (Blueprint $table) {
            $table->id();

            // home, about — the key PAGE_SEO is addressed by.
            $table->string('key')->unique();

            // Path without a language prefix; the English URL is derived.
            $table->string('path');

            // Null on both means the bare site title, with no page name in
            // front of it.
            $table->string('title_id')->nullable();
            $table->string('title_en')->nullable();

            /*
             * Descriptions are taken from copy that already exists on the page
             * rather than written separately, so they cannot drift from it.
             * This names the UI string to lift the opening sentence of.
             */
            $table->string('description_key')->nullable();

            // Used only when no description_key is set — an authored
            // description for a page whose copy does not supply one.
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seos');
    }
};
