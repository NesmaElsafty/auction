<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 default user
        User::factory()->create([
            'name' => 'Default User',
            'national_id' => '1000000001',
            'phone' => '+1234567890',
            'email' => 'nesmaelsaftysm@gmail.com',
            'address' => '123 Main Street, City, Country',
            'summary' => 'This is the default user account.',
            'link' => 'https://example.com/user',
            'password' => Hash::make(123456),
            'type' => 'user',
            'is_active' => true,
        ]);

        // Create 50 additional users using factory
        User::factory(50)->create();
    }
}

