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
            'password' => bcrypt('owner123'),
            'role' => 'owner'
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Kasir',
            'username' => 'kasir',
            'password' => bcrypt('kasir123'),
            'role' => 'kasir'
        ]);
    }
}
