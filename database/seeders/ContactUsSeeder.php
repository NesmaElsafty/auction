<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactUs; // Added import for ContactUs model

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact_us = [
            'phone' => "96612345678",
            'email' => "auction@sa.com",
            'copyright' => 'copyright'
        ];

        ContactUs::create([
            'phone' => $contact_us['phone'],
            'email' => $contact_us['email'],
            'copyright' => $contact_us['copyright'],
        ]);
    }
}
