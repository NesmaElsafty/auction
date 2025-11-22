<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Input;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $optionSets = $this->getOptionSets();
        $randomSet = fake()->randomElement($optionSets);
        $option = fake()->randomElement($randomSet['options']);

        return [
            'input_id' => Input::factory(),
            'value' => $option['value'],
            'label' => $option['label'],
        ];
    }

    /**
     * Get predefined option sets
     */
    private function getOptionSets(): array
    {
        return [
            [
                'name' => 'cities',
                'options' => [
                    ['value' => 'riyadh', 'label' => 'الرياض'],
                    ['value' => 'jeddah', 'label' => 'جدة'],
                    ['value' => 'dammam', 'label' => 'الدمام'],
                    ['value' => 'makkah', 'label' => 'مكة المكرمة'],
                    ['value' => 'madina', 'label' => 'المدينة المنورة'],
                    ['value' => 'khobar', 'label' => 'الخبر'],
                    ['value' => 'taif', 'label' => 'الطائف'],
                    ['value' => 'abha', 'label' => 'أبها'],
                ],
            ],
            [
                'name' => 'districts',
                'options' => [
                    ['value' => 'north', 'label' => 'الشمال'],
                    ['value' => 'south', 'label' => 'الجنوب'],
                    ['value' => 'east', 'label' => 'الشرق'],
                    ['value' => 'west', 'label' => 'الغرب'],
                    ['value' => 'center', 'label' => 'الوسط'],
                ],
            ],
            [
                'name' => 'conditions',
                'options' => [
                    ['value' => 'excellent', 'label' => 'ممتاز'],
                    ['value' => 'very_good', 'label' => 'جيد جداً'],
                    ['value' => 'good', 'label' => 'جيد'],
                    ['value' => 'fair', 'label' => 'متوسط'],
                    ['value' => 'poor', 'label' => 'ضعيف'],
                    ['value' => 'damaged', 'label' => 'تالف'],
                ],
            ],
            [
                'name' => 'status',
                'options' => [
                    ['value' => 'active', 'label' => 'نشط'],
                    ['value' => 'inactive', 'label' => 'غير نشط'],
                    ['value' => 'pending', 'label' => 'قيد الانتظار'],
                    ['value' => 'approved', 'label' => 'موافق عليه'],
                    ['value' => 'rejected', 'label' => 'مرفوض'],
                    ['value' => 'completed', 'label' => 'مكتمل'],
                ],
            ],
            [
                'name' => 'vehicle_types',
                'options' => [
                    ['value' => 'sedan', 'label' => 'سيدان'],
                    ['value' => 'suv', 'label' => 'دفع رباعي'],
                    ['value' => 'truck', 'label' => 'شاحنة'],
                    ['value' => 'motorcycle', 'label' => 'دراجة نارية'],
                    ['value' => 'bus', 'label' => 'حافلة'],
                ],
            ],
            [
                'name' => 'fuel_types',
                'options' => [
                    ['value' => 'petrol', 'label' => 'بنزين'],
                    ['value' => 'diesel', 'label' => 'ديزل'],
                    ['value' => 'electric', 'label' => 'كهربائي'],
                    ['value' => 'hybrid', 'label' => 'هجين'],
                ],
            ],
            [
                'name' => 'property_types',
                'options' => [
                    ['value' => 'apartment', 'label' => 'شقة'],
                    ['value' => 'villa', 'label' => 'فيلا'],
                    ['value' => 'house', 'label' => 'منزل'],
                    ['value' => 'commercial', 'label' => 'تجاري'],
                    ['value' => 'office', 'label' => 'مكتب'],
                ],
            ],
            [
                'name' => 'priority',
                'options' => [
                    ['value' => 'low', 'label' => 'منخفض'],
                    ['value' => 'medium', 'label' => 'متوسط'],
                    ['value' => 'high', 'label' => 'عالي'],
                    ['value' => 'urgent', 'label' => 'عاجل'],
                ],
            ],
        ];
    }

    /**
     * Create options for a specific input
     */
    public function forInput(Input $input): static
    {
        return $this->state(function (array $attributes) use ($input) {
            return [
                'input_id' => $input->id,
            ];
        });
    }

    /**
     * Create options for cities
     */
    public function cities(): static
    {
        return $this->state(function (array $attributes) {
            $cities = [
                ['value' => 'riyadh', 'label' => 'الرياض'],
                ['value' => 'jeddah', 'label' => 'جدة'],
                ['value' => 'dammam', 'label' => 'الدمام'],
                ['value' => 'makkah', 'label' => 'مكة المكرمة'],
                ['value' => 'madina', 'label' => 'المدينة المنورة'],
            ];
            $city = fake()->randomElement($cities);
            return [
                'value' => $city['value'],
                'label' => $city['label'],
            ];
        });
    }

    /**
     * Create options for conditions
     */
    public function conditions(): static
    {
        return $this->state(function (array $attributes) {
            $conditions = [
                ['value' => 'excellent', 'label' => 'ممتاز'],
                ['value' => 'very_good', 'label' => 'جيد جداً'],
                ['value' => 'good', 'label' => 'جيد'],
                ['value' => 'fair', 'label' => 'متوسط'],
                ['value' => 'poor', 'label' => 'ضعيف'],
            ];
            $condition = fake()->randomElement($conditions);
            return [
                'value' => $condition['value'],
                'label' => $condition['label'],
            ];
        });
    }
}

