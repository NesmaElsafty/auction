<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 default admin
        User::factory()->create([
            'name' => 'Default Admin',
            'national_id' => '2000000001',
            'phone' => '+1234567891',
            'email' => 'nesmaelsaftysm@gmail.com',
            'address' => '456 Admin Avenue, City, Country',
            'summary' => 'This is the default admin account.',
            'link' => 'https://example.com/admin',
            'password' => Hash::make(123456),
            'type' => 'admin',
            'is_active' => true,
        ]);

        // Create 50 additional admins using factory
        User::factory(50)->admin()->create();
    }
}

