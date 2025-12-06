<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active users with type 'user'
        $users = User::where('type', 'user')
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No active users found. Please run UserSeeder first.');
            return;
        }

        // Create alerts for each user
        foreach ($users as $user) {
            // Create 3-7 random alerts per user
            $alertCount = fake()->numberBetween(3, 7);
            
            Alert::factory($alertCount)->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('Alerts created successfully for ' . $users->count() . ' users.');
    }
}

