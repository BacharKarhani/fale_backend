<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomeVideo;

class HomeVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomeVideo::create([
            'title' => 'Welcome to FALE Festival',
            'description' => 'Experience the magic of our annual festival with amazing performances, delicious food, and unforgettable memories. Upload your video file to showcase your event.',
            'is_active' => true,
        ]);
    }
}
