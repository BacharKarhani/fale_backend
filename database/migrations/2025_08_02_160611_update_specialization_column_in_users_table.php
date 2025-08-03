<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('specialization')->nullable()->change(); // or ->json() if using MySQL 5.7+
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialization', 255)->nullable()->change();
        });
    }

};
