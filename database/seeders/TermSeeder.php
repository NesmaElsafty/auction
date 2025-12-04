<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create terms for each type
        Term::factory(3)->create(['type' => 'terms']);
        Term::factory(2)->create(['type' => 'privacy_policy']);
        Term::factory(5)->create(['type' => 'faqs']);
    }
}

