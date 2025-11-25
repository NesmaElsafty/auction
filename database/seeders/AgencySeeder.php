<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create agencies for existing users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create 1-3 agencies per user (random)
        foreach ($users as $user) {
            $agencyCount = rand(1, 3);
            
            Agency::factory($agencyCount)
                ->forUser($user)
                ->create();
        }
    }
}

