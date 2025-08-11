<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('phone_number', 30)->nullable()->index();
            $table->string('qr_code')->nullable(); // storage path like: storage/qr_admin_employees/employee_1.png
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_employees');
    }
};
