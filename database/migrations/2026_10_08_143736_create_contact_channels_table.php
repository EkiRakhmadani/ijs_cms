<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_channels', function (Blueprint $table) {
            $table->id();

            // phone, email, instagram, linkedin, address — the key the
            // frontend's CONTACT object is addressed by.
            $table->string('key')->unique();

            /*
             * Kept apart from the label on purpose: the number dials as
             * +622157973088 and reads as +6221-5797-3088, and those are not
             * the same string.
             */
            $table->string('href');

            /*
             * One per language. Most channels read the same either way and
             * carry only the Indonesian side; the address genuinely differs,
             * because the words in it translate while the proper names do not.
             */
            $table->text('label_id');
            $table->text('label_en')->nullable();

            /*
             * schema.org PostalAddress fields, for the structured data search
             * engines read. Only the address channel has them, and they are
             * not for display.
             */
            $table->json('postal')->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_channels');
    }
};
