<?php

namespace Database\Seeders;

use App\Models\Input;
use App\Models\Screen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InputSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all screens
        $screens = Screen::all();

        if ($screens->isEmpty()) {
            $this->command->warn('No screens found. Please run ScreenSeeder first.');
            return;
        }

        // Define inputs for each screen based on screen title
        foreach ($screens as $screen) {
            $inputs = $this->getInputsForScreen($screen->title);
            
            foreach ($inputs as $inputData) {
                $input = Input::create([
                    'screen_id' => $screen->id,
                    'name' => $inputData['name'],
                    'type' => $inputData['type'],
                    'placeholder' => $inputData['placeholder'] ?? null,
                    'label' => $inputData['label'],
                    'is_required' => $inputData['is_required'] ?? false,
                ]);

                // If it's a select input, we'll create options in OptionSeeder
                // But we can also create them here if needed
                if ($input->type === 'select' && isset($inputData['options'])) {
                    foreach ($inputData['options'] as $optionData) {
                        $input->options()->create([
                            'value' => $optionData['value'],
                            'label' => $optionData['label'],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Inputs seeded successfully!');
    }

    /**
     * Get inputs based on screen title
     */
    private function getInputsForScreen(?string $screenTitle): array
    {
        // Default inputs if screen title doesn't match
        $defaultInputs = [
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'الاسم',
                'placeholder' => 'أدخل الاسم',
                'is_required' => true,
            ],
            [
                'name' => 'description',
                'type' => 'textarea',
                'label' => 'الوصف',
                'placeholder' => 'أدخل الوصف',
                'is_required' => false,
            ],
        ];

        if (!$screenTitle) {
            return $defaultInputs;
        }

        // Map screen titles to specific inputs
        $screenInputs = [
            'أرقام المركبة' => [
                ['name' => 'plate_number', 'type' => 'text', 'label' => 'رقم اللوحة', 'placeholder' => 'أدخل رقم اللوحة', 'is_required' => true],
                ['name' => 'chassis_number', 'type' => 'text', 'label' => 'رقم الهيكل', 'placeholder' => 'أدخل رقم الهيكل', 'is_required' => true],
                ['name' => 'serial_number', 'type' => 'text', 'label' => 'الرقم التسلسلي', 'placeholder' => 'أدخل الرقم التسلسلي', 'is_required' => false],
                ['name' => 'kilometers', 'type' => 'number', 'label' => 'الكيلومترات', 'placeholder' => 'أدخل عدد الكيلومترات', 'is_required' => false],
            ],
            'تفاصيل السيارة' => [
                ['name' => 'manufacturer', 'type' => 'text', 'label' => 'الشركة المصنعة', 'placeholder' => 'أدخل الشركة المصنعة', 'is_required' => true],
                ['name' => 'model', 'type' => 'text', 'label' => 'الطراز', 'placeholder' => 'أدخل الطراز', 'is_required' => true],
                ['name' => 'category', 'type' => 'select', 'label' => 'الفئة', 'placeholder' => 'اختر الفئة', 'is_required' => true, 'options' => [
                    ['value' => 'sedan', 'label' => 'سيدان'],
                    ['value' => 'suv', 'label' => 'دفع رباعي'],
                    ['value' => 'truck', 'label' => 'شاحنة'],
                ]],
                ['name' => 'year', 'type' => 'number', 'label' => 'سنة الصنع', 'placeholder' => 'أدخل سنة الصنع', 'is_required' => true],
            ],
            'مواصفات السيارة' => [
                ['name' => 'exterior_color', 'type' => 'text', 'label' => 'اللون الخارجي', 'placeholder' => 'أدخل اللون الخارجي', 'is_required' => false],
                ['name' => 'interior_color', 'type' => 'text', 'label' => 'اللون الداخلي', 'placeholder' => 'أدخل اللون الداخلي', 'is_required' => false],
                ['name' => 'fuel_type', 'type' => 'select', 'label' => 'نوع الوقود', 'placeholder' => 'اختر نوع الوقود', 'is_required' => true, 'options' => [
                    ['value' => 'petrol', 'label' => 'بنزين'],
                    ['value' => 'diesel', 'label' => 'ديزل'],
                    ['value' => 'electric', 'label' => 'كهربائي'],
                ]],
                ['name' => 'transmission', 'type' => 'select', 'label' => 'ناقل الحركة', 'placeholder' => 'اختر ناقل الحركة', 'is_required' => true, 'options' => [
                    ['value' => 'manual', 'label' => 'يدوي'],
                    ['value' => 'automatic', 'label' => 'أوتوماتيك'],
                ]],
            ],
            'معلومات الأرض' => [
                ['name' => 'plot_number', 'type' => 'text', 'label' => 'رقم القطعة', 'placeholder' => 'أدخل رقم القطعة', 'is_required' => true],
                ['name' => 'plan_number', 'type' => 'text', 'label' => 'رقم المخطط', 'placeholder' => 'أدخل رقم المخطط', 'is_required' => false],
                ['name' => 'survey_number', 'type' => 'text', 'label' => 'الرقم المساحي', 'placeholder' => 'أدخل الرقم المساحي', 'is_required' => false],
            ],
            'موقع الأرض' => [
                ['name' => 'address', 'type' => 'text', 'label' => 'العنوان الكامل', 'placeholder' => 'أدخل العنوان الكامل', 'is_required' => true],
                ['name' => 'city', 'type' => 'select', 'label' => 'المدينة', 'placeholder' => 'اختر المدينة', 'is_required' => true, 'options' => [
                    ['value' => 'riyadh', 'label' => 'الرياض'],
                    ['value' => 'jeddah', 'label' => 'جدة'],
                    ['value' => 'dammam', 'label' => 'الدمام'],
                ]],
                ['name' => 'district', 'type' => 'select', 'label' => 'المنطقة', 'placeholder' => 'اختر المنطقة', 'is_required' => true, 'options' => [
                    ['value' => 'north', 'label' => 'الشمال'],
                    ['value' => 'south', 'label' => 'الجنوب'],
                    ['value' => 'east', 'label' => 'الشرق'],
                ]],
            ],
            'مساحة الأرض' => [
                ['name' => 'area', 'type' => 'number', 'label' => 'المساحة بالمتر المربع', 'placeholder' => 'أدخل المساحة', 'is_required' => true],
                ['name' => 'length', 'type' => 'number', 'label' => 'الطول', 'placeholder' => 'أدخل الطول', 'is_required' => false],
                ['name' => 'width', 'type' => 'number', 'label' => 'العرض', 'placeholder' => 'أدخل العرض', 'is_required' => false],
            ],
            'معلومات العقار' => [
                ['name' => 'property_type', 'type' => 'select', 'label' => 'نوع العقار', 'placeholder' => 'اختر نوع العقار', 'is_required' => true, 'options' => [
                    ['value' => 'apartment', 'label' => 'شقة'],
                    ['value' => 'villa', 'label' => 'فيلا'],
                    ['value' => 'house', 'label' => 'منزل'],
                ]],
                ['name' => 'floors', 'type' => 'number', 'label' => 'عدد الطوابق', 'placeholder' => 'أدخل عدد الطوابق', 'is_required' => false],
                ['name' => 'units', 'type' => 'number', 'label' => 'عدد الوحدات', 'placeholder' => 'أدخل عدد الوحدات', 'is_required' => false],
            ],
            'معاينة السيارة' => [
                ['name' => 'inspection_location', 'type' => 'text', 'label' => 'موقع المعاينة', 'placeholder' => 'أدخل موقع المعاينة', 'is_required' => true],
                ['name' => 'district', 'type' => 'select', 'label' => 'المنطقة', 'placeholder' => 'اختر المنطقة', 'is_required' => true, 'options' => [
                    ['value' => 'north', 'label' => 'الشمال'],
                    ['value' => 'south', 'label' => 'الجنوب'],
                    ['value' => 'east', 'label' => 'الشرق'],
                ]],
                ['name' => 'inspection_time', 'type' => 'datetime', 'label' => 'وقت المعاينة', 'placeholder' => 'اختر وقت المعاينة', 'is_required' => true],
            ],
        ];

        return $screenInputs[$screenTitle] ?? $defaultInputs;
    }
}

