<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoothAreaSeeder extends Seeder
{
    public function run()
    {
        $booths = [];

        // Add booths from 1 to 112
        for ($i = 1; $i <= 112; $i++) {
            $booths[] = [
                'label' => (string) $i,
                'dimensions' => '3x3', // Default, adjust later
                'price' => 2500,
                'benefits' => 'Standard booth benefits',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Add special areas
        $booths[] = [
            'label' => 'WHOLESOME CRAFT',
            'dimensions' => 'Special',
            'price' => 0,
            'benefits' => 'Reserved for special crafts',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('booth_areas')->insert($booths);
    }
}
