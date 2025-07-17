<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventDirectionsTable extends Migration
{
    public function up()
    {
        Schema::create('event_directions', function (Blueprint $table) {
            $table->id();
            $table->string('section_tagline');
            $table->string('section_title');
            $table->text('description');
            $table->string('call_text');
            $table->string('call_number');
            $table->string('call_icon')->nullable(); // e.g., event-direction-chat-icon.png
            $table->json('counters'); // Store counters as JSON array
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_directions');
    }
}
