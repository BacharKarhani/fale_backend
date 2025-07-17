<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sliding_texts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('hover_text')->nullable();
            $table->string('icon')->nullable(); // Path to image file
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliding_texts');
    }
};
