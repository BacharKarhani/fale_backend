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
        Schema::table('home_videos', function (Blueprint $table) {
            $table->dropColumn('video_url');
            $table->string('video_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_videos', function (Blueprint $table) {
            $table->dropColumn('video_file');
            $table->string('video_url', 500);
        });
    }
};
