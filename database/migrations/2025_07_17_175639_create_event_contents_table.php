<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_tagline');
            $table->string('section_title');
            $table->string('icon_1_class');
            $table->string('icon_1_subtitle');
            $table->text('icon_1_subtagline');
            $table->string('icon_2_class');
            $table->string('icon_2_subtitle');
            $table->text('icon_2_subtagline');
            $table->string('event_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_contents');
    }
};
