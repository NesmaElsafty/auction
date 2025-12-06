<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create notifications for each type
        Notification::factory(5)->create(['type' => 'notify']);
        Notification::factory(5)->create(['type' => 'reminder']);
    }
}

