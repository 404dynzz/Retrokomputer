<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Owner',
            'username' => 'owner',
            'email' => 'owner@retrokomputer.com',
            'password' => bcrypt('owner123'),
            'role' => 'owner'
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@retrokomputer.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Kasir',
            'username' => 'kasir',
            'email' => 'kasir@retrokomputer.com',
            'password' => bcrypt('kasir123'),
            'role' => 'kasir'
        ]);
    }
}
