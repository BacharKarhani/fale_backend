<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_ticket_plans_table.php
public function up()
{
    Schema::create('ticket_plans', function (Blueprint $table) {
        $table->id();
        $table->string('plan_name');
        $table->decimal('price', 8, 2);
        $table->json('features');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_plans');
    }
};
