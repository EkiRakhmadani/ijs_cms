<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();

            // The Instagram permalink the panel leads to.
            $table->string('url');

            /*
             * With a picture the panel is that picture; without one it is
             * Instagram's own embed of the permalink. Both routes are
             * supported deliberately — see the note in the frontend's
             * data/socialFeed.js for why they are genuinely different trades.
             */
            $table->foreignId('media_asset_id')->nullable()->constrained()->nullOnDelete();

            // Announced to screen readers, so bilingual like the rest.
            $table->text('caption_id')->nullable();
            $table->text('caption_en')->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
