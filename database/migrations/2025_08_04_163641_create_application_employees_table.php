<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('application_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booth_application_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamps();

            $table->foreign('booth_application_id')
                  ->references('id')->on('booth_applications')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('application_employees');
    }
}
