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
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->string('stats_section_title')->nullable()->default('Connect & Collaborate');
            $table->integer('exhibitors_count')->nullable()->default(120);
            $table->integer('visitors_count')->nullable()->default(15000);
            $table->integer('panels_count')->nullable()->default(12);
            $table->boolean('stats_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'stats_section_title',
                'exhibitors_count',
                'visitors_count',
                'panels_count',
                'stats_enabled'
            ]);
        });
    }
};
