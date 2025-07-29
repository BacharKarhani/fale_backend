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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('gender');
            $table->string('specialization')->nullable()->after('company_name');
            $table->text('geographical_scope')->nullable()->after('specialization');
            $table->string('code', 50)->nullable()->after('geographical_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'specialization', 
                'geographical_scope',
                'code'
            ]);
        });
    }
};