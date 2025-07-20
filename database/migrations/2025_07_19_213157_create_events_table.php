<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image'); // Image filename
            $table->string('time');  // Example: "10 AM To 10 PM - 20 April 2024"
            $table->string('address'); // Example: "Mirpur 01 Road N 12 Dhaka, Bangladesh"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
