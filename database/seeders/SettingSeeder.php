<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create fixed auction settings
        Setting::create([
            'name' => 'auction_percentage_alert',
            'type' => 'auction',
            'value' => '10',
            'value_type' => 'percentage',
        ]);

        Setting::create([
            'name' => 'negotiation_times',
            'type' => 'auction',
            'value' => '3',
            'value_type' => 'value',
        ]);

        Setting::create([
            'name' => 'auction_max_period',
            'type' => 'auction',
            'value' => '30',
            'value_type' => 'value',
        ]);

        // Create fixed finance settings
        Setting::create([
            'name' => 'brokerage_fee',
            'type' => 'finance',
            'value' => '5',
            'value_type' => 'percentage',
        ]);

        Setting::create([
            'name' => 'vat_value',
            'type' => 'finance',
            'value' => '15',
            'value_type' => 'value',
        ]);

        Setting::create([
            'name' => 'administrative_fees',
            'type' => 'finance',
            'value' => '2',
            'value_type' => 'percentage',
        ]);

        Setting::create([
            'name' => 'ownership_transfer_fees',
            'type' => 'finance',
            'value' => '1',
            'value_type' => 'percentage',
        ]);

        Setting::create([
            'name' => 'profit_percentage',
            'type' => 'finance',
            'value' => '10',
            'value_type' => 'percentage',
        ]);
    }
}

