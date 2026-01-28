<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Admin::create([
            'name' => 'Shakil Hossain',
            'email' => 'admin@example.com',
            'password' => Hash::make('password')
        ]);

        User::create([
            'name' => 'shamim Hossain',
            'email' => 'shamimhossen6622@gmail.com',
            'number' => '01766228406',
            'password' => Hash::make('12345678')
        ]);
    }
}
