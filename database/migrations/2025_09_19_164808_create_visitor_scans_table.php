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
        Schema::create('visitor_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scanner_id'); // Admin or Company user who scanned
            $table->string('scanner_type'); // 'admin' or 'company'
            $table->unsignedBigInteger('employee_id'); // The employee whose QR was scanned
            $table->string('employee_type'); // 'admin_employee' or 'application_employee'
            $table->string('scanner_name'); // Name of the person who scanned
            $table->string('scanner_company')->nullable(); // Company of the scanner
            $table->string('employee_name'); // Name of the scanned employee
            $table->string('employee_company')->nullable(); // Company of the scanned employee
            $table->timestamp('scan_time'); // When the scan happened
            $table->string('location')->nullable(); // Optional location where scan happened
            $table->text('notes')->nullable(); // Optional notes about the scan
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['scanner_id', 'scanner_type']);
            $table->index(['employee_id', 'employee_type']);
            $table->index('scan_time');
            $table->index(['scanner_company', 'scan_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_scans');
    }
};
