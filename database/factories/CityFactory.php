<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $saudiCities = [
            'الرياض',
            'جدة',
            'مكة المكرمة',
            'المدينة المنورة',
            'الدمام',
            'الخبر',
            'الطائف',
            'بريدة',
            'تبوك',
            'خميس مشيط',
            'حائل',
            'الجبيل',
            'نجران',
            'أبها',
            'جازان',
            'ينبع',
            'الباحة',
            'سكاكا',
            'عرعر',
            'الرس',
            'عنيزة',
            'الزلفي',
            'الدوادمي',
            'الخرج',
            'المجمعة',
            'القريات',
            'شرورة',
            'طريف',
            'القنفذة',
            'صبيا',
        ];

        return [
            'name' => fake()->unique()->randomElement($saudiCities),
        ];
    }
}

