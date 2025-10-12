<?php

namespace Database\Seeders;

use App\Models\Donation;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100 donations
        Donation::factory()
            ->count(100)
            ->create();

        // Create some additional paid donations with specific amounts
        Donation::factory()
            ->paid()
            ->count(10)
            ->create([
                'amount' => 1000000, // 1M donations
            ]);

        // Create some anonymous donations
        Donation::factory()
            ->paid()
            ->anonymous()
            ->count(5)
            ->create();

        $this->command->info('✅ Successfully created ' . Donation::count() . ' donations');
        $this->command->info('   - Paid: ' . Donation::where('status', 'paid')->count());
        $this->command->info('   - Pending: ' . Donation::where('status', 'pending')->count());
        $this->command->info('   - Failed: ' . Donation::where('status', 'failed')->count());
    }
}

