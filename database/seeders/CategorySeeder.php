<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories with screens
        Category::factory(4)->create()->each(function ($category) {
            // Create 3-5 screens for each category with matching screen types
            \App\Models\Screen::factory(rand(3, 5))
                ->forCategory($category->name)
                ->create([
                    'category_id' => $category->id,
                ]);
        });
    }
}
