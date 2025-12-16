<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'Auction',
            'Category',
            'User',
            'Agency',
            'Notification',
            'Alert',
            'System',
        ];

        $settingNames = [
            'max_bid_amount',
            'min_bid_amount',
            'auction_duration_days',
            'commission_rate',
            'tax_rate',
            'currency',
            'timezone',
            'site_name',
            'site_email',
            'site_phone',
            'maintenance_mode',
            'registration_enabled',
            'email_verification_required',
            'max_file_size',
            'allowed_file_types',
        ];

        $settingValues = [
            '1000000',
            '100',
            '7',
            '5.5',
            '15',
            'SAR',
            'Asia/Riyadh',
            'منصة المزادات',
            'info@auction.com',
            '+966500000000',
            'false',
            'true',
            'false',
            '10485760',
            'jpg,png,pdf,doc,docx',
        ];

        return [
            'type' => fake()->randomElement($types),
            'name' => fake()->randomElement($settingNames),
            'value' => fake()->randomElement($settingValues),
        ];
    }
}

