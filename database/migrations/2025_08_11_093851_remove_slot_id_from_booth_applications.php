<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('booth_applications', function (Blueprint $table) {
        $table->unsignedBigInteger('slot_id')->nullable()->change();
    });
}


public function down()
{
    Schema::table('booth_applications', function (Blueprint $table) {
        $table->unsignedBigInteger('slot_id')->nullable();
        // $table->foreign('slot_id')->references('id')->on('booth_area_slots')->onDelete('cascade');
    });
}

};
