<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ui_strings', function (Blueprint $table) {
            $table->id();

            // Dotted path, grouped by the surface it appears on: nav.home,
            // footer.rights, services.lever, and so on.
            $table->string('key')->unique();

            // The first segment of the key, stored so the panel can group and
            // filter without parsing the key in every query.
            $table->string('group')->index();

            $table->text('value_id');
            $table->text('value_en');

            // Authoring order, kept so the generated file reads grouped by
            // the surface each string appears on rather than alphabetically.
            $table->unsignedSmallInteger('position')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_strings');
    }
};
