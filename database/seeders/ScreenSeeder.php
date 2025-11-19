<?php

namespace Database\Seeders;

use App\Models\Screen;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScreenSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Screens are already created in CategorySeeder
        // This seeder can be used if you need standalone screens
        if (Category::count() > 0) {
            Screen::factory(20)->create([
                'category_id' => Category::inRandomOrder()->first()->id,
            ]);
        }
    }
}

