<?php

namespace Database\Seeders;

use App\Models\Input;
use App\Models\Option;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all select inputs that don't have options yet
        $selectInputs = Input::where('type', 'select')
            ->whereDoesntHave('options')
            ->get();

        if ($selectInputs->isEmpty()) {
            $this->command->info('No select inputs found without options.');
            return;
        }

        foreach ($selectInputs as $input) {
            // Generate options based on input name
            $options = $this->getOptionsForInput($input->name, $input->label);
            
            foreach ($options as $optionData) {
                Option::create([
                    'input_id' => $input->id,
                    'value' => $optionData['value'],
                    'label' => $optionData['label'],
                ]);
            }
        }

        $this->command->info('Options seeded successfully!');
    }

    /**
     * Get options based on input name
     */
    private function getOptionsForInput(string $inputName, ?string $inputLabel): array
    {
        $optionSets = [
            'category' => [
                ['value' => 'sedan', 'label' => 'سيدان'],
                ['value' => 'suv', 'label' => 'دفع رباعي'],
                ['value' => 'truck', 'label' => 'شاحنة'],
                ['value' => 'motorcycle', 'label' => 'دراجة نارية'],
            ],
            'city' => [
                ['value' => 'riyadh', 'label' => 'الرياض'],
                ['value' => 'jeddah', 'label' => 'جدة'],
                ['value' => 'dammam', 'label' => 'الدمام'],
                ['value' => 'makkah', 'label' => 'مكة المكرمة'],
                ['value' => 'madina', 'label' => 'المدينة المنورة'],
                ['value' => 'khobar', 'label' => 'الخبر'],
                ['value' => 'taif', 'label' => 'الطائف'],
            ],
            'district' => [
                ['value' => 'north', 'label' => 'الشمال'],
                ['value' => 'south', 'label' => 'الجنوب'],
                ['value' => 'east', 'label' => 'الشرق'],
                ['value' => 'west', 'label' => 'الغرب'],
                ['value' => 'center', 'label' => 'الوسط'],
            ],
            'condition' => [
                ['value' => 'excellent', 'label' => 'ممتاز'],
                ['value' => 'very_good', 'label' => 'جيد جداً'],
                ['value' => 'good', 'label' => 'جيد'],
                ['value' => 'fair', 'label' => 'متوسط'],
                ['value' => 'poor', 'label' => 'ضعيف'],
                ['value' => 'damaged', 'label' => 'تالف'],
            ],
            'status' => [
                ['value' => 'active', 'label' => 'نشط'],
                ['value' => 'inactive', 'label' => 'غير نشط'],
                ['value' => 'pending', 'label' => 'قيد الانتظار'],
                ['value' => 'approved', 'label' => 'موافق عليه'],
                ['value' => 'rejected', 'label' => 'مرفوض'],
            ],
            'type' => [
                ['value' => 'apartment', 'label' => 'شقة'],
                ['value' => 'villa', 'label' => 'فيلا'],
                ['value' => 'house', 'label' => 'منزل'],
                ['value' => 'commercial', 'label' => 'تجاري'],
            ],
            'fuel_type' => [
                ['value' => 'petrol', 'label' => 'بنزين'],
                ['value' => 'diesel', 'label' => 'ديزل'],
                ['value' => 'electric', 'label' => 'كهربائي'],
                ['value' => 'hybrid', 'label' => 'هجين'],
            ],
            'transmission' => [
                ['value' => 'manual', 'label' => 'يدوي'],
                ['value' => 'automatic', 'label' => 'أوتوماتيك'],
            ],
            'property_type' => [
                ['value' => 'apartment', 'label' => 'شقة'],
                ['value' => 'villa', 'label' => 'فيلا'],
                ['value' => 'house', 'label' => 'منزل'],
                ['value' => 'commercial', 'label' => 'تجاري'],
                ['value' => 'office', 'label' => 'مكتب'],
            ],
            'priority' => [
                ['value' => 'low', 'label' => 'منخفض'],
                ['value' => 'medium', 'label' => 'متوسط'],
                ['value' => 'high', 'label' => 'عالي'],
                ['value' => 'urgent', 'label' => 'عاجل'],
            ],
        ];

        // Try to match by input name
        if (isset($optionSets[$inputName])) {
            return $optionSets[$inputName];
        }

        // Try to match by label if name doesn't match
        if ($inputLabel) {
            $labelMappings = [
                'الفئة' => 'category',
                'المدينة' => 'city',
                'المنطقة' => 'district',
                'الحالة' => 'condition',
                'النوع' => 'type',
                'نوع الوقود' => 'fuel_type',
                'ناقل الحركة' => 'transmission',
                'نوع العقار' => 'property_type',
                'الأولوية' => 'priority',
            ];

            foreach ($labelMappings as $label => $key) {
                if (str_contains($inputLabel, $label) && isset($optionSets[$key])) {
                    return $optionSets[$key];
                }
            }
        }

        // Default options if no match found
        return [
            ['value' => 'option1', 'label' => 'خيار 1'],
            ['value' => 'option2', 'label' => 'خيار 2'],
            ['value' => 'option3', 'label' => 'خيار 3'],
        ];
    }
}

