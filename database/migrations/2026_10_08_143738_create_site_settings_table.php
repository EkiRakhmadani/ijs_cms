<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row. The site has one identity; a key/value table would only
        // make every read a lookup and every form a guess.
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            /*
             * The one address the site is known by. ptijs.com and
             * www.ptijs.com both point here, and www 301-redirects to it, or
             * search engines see two copies of every page.
             */
            $table->string('url');

            $table->string('title');
            $table->string('name');
            $table->string('legal_name');
            $table->string('short_name');

            $table->string('locale_id')->default('id_ID');
            $table->string('locale_en')->default('en_US');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
