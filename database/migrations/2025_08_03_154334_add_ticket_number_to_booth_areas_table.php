<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketNumberToBoothAreasTable extends Migration
{
    public function up()
    {
        Schema::table('booth_areas', function (Blueprint $table) {
            $table->string('ticket_number')->nullable()->after('benefits');
        });
    }

    public function down()
    {
        Schema::table('booth_areas', function (Blueprint $table) {
            $table->dropColumn('ticket_number');
        });
    }
}

