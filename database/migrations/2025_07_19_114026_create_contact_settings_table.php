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
Schema::create('contact_settings', function (Blueprint $table) {
    $table->id();
    $table->string('location')->nullable();
    $table->json('emails')->nullable();    // store as ["email1", "email2"]
    $table->json('phones')->nullable();    // store as ["phone1", "phone2"]
    $table->json('services')->nullable();  // store as ["Service 1", "Service 2"]
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
