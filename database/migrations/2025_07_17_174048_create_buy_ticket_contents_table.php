<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyTicketContentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('buy_ticket_contents', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('timing');
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable(); // Store image path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_ticket_contents');
    }
}
