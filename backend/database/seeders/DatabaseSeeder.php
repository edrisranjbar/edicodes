<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Add the ContactSeeder to the seeders list
        $this->call([
            ContactSeeder::class,
        ]);

        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
        ]);

        // Call AdminSeeder
        $this->call([
            AdminSeeder::class,
        ]);

        // Call DonationSeeder
        $this->call([
            DonationSeeder::class,
        ]);
    }
}
