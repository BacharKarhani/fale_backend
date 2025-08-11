<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('admin_employees', function (Blueprint $table) {
            $table->string('company')->default('LafeLeb')->after('phone_number');
        });

        // عدّل السجلات الموجودة حالياً لتاخد القيمة إذا كانت null
        DB::table('admin_employees')
            ->whereNull('company')
            ->update(['company' => 'LafeLeb']);
    }

    public function down(): void
    {
        Schema::table('admin_employees', function (Blueprint $table) {
            $table->dropColumn('company');
        });
    }
};
