<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'user'],
        ]);


        // Seed an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone' => '+96171111222',
            'dob' => '1990-01-01',
            'gender' => 'male',
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]);

        // Seed a normal user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+96176677888',
            'dob' => '1995-05-10',
            'gender' => 'female',
            'role_id' => Role::where('name', 'user')->first()->id,
        ]);
    }
}
