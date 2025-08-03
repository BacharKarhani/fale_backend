<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
  Schema::create('booth_areas', function (Blueprint $table) {
    $table->id();
    $table->string('label'); // e.g. "1", "2", "A1"
    $table->string('dimensions'); // e.g. "3x6"
    $table->decimal('price', 10, 2);
    $table->text('benefits')->nullable(); // optional
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booth_areas');
    }
};
