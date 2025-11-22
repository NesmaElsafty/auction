<?php

namespace Database\Factories;

use App\Models\Input;
use App\Models\Screen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Input>
 */
class InputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['text', 'number', 'email', 'password', 'date', 'time', 'datetime', 'checkbox', 'radio', 'select', 'textarea', 'file', 'image', 'video', 'audio'];
        $type = fake()->randomElement($types);

        // Generate appropriate data based on input type
        $inputData = $this->getInputDataByType($type);

        return [
            'screen_id' => Screen::factory(),
            'name' => $inputData['name'],
            'type' => $type,
            'placeholder' => $inputData['placeholder'],
            'label' => $inputData['label'],
            'is_required' => fake()->boolean(30), // 30% chance of being required
        ];
    }

    /**
     * Get input data based on type
     */
    private function getInputDataByType(string $type): array
    {
        $data = [
            'text' => [
                'name' => fake()->randomElement(['name', 'title', 'description', 'address', 'phone', 'notes']),
                'label' => fake()->randomElement(['الاسم', 'العنوان', 'العنوان الكامل', 'رقم الهاتف', 'ملاحظات', 'الوصف']),
                'placeholder' => fake()->randomElement(['أدخل الاسم', 'أدخل العنوان', 'أدخل رقم الهاتف', 'أدخل الملاحظات']),
            ],
            'number' => [
                'name' => fake()->randomElement(['price', 'quantity', 'area', 'year', 'kilometers', 'floors']),
                'label' => fake()->randomElement(['السعر', 'الكمية', 'المساحة', 'السنة', 'الكيلومترات', 'عدد الطوابق']),
                'placeholder' => fake()->randomElement(['أدخل السعر', 'أدخل الكمية', 'أدخل المساحة', 'أدخل السنة']),
            ],
            'email' => [
                'name' => 'email',
                'label' => 'البريد الإلكتروني',
                'placeholder' => 'أدخل البريد الإلكتروني',
            ],
            'password' => [
                'name' => 'password',
                'label' => 'كلمة المرور',
                'placeholder' => 'أدخل كلمة المرور',
            ],
            'date' => [
                'name' => fake()->randomElement(['date', 'birth_date', 'expiry_date', 'inspection_date']),
                'label' => fake()->randomElement(['التاريخ', 'تاريخ الميلاد', 'تاريخ الانتهاء', 'تاريخ المعاينة']),
                'placeholder' => 'اختر التاريخ',
            ],
            'time' => [
                'name' => 'time',
                'label' => 'الوقت',
                'placeholder' => 'اختر الوقت',
            ],
            'datetime' => [
                'name' => fake()->randomElement(['datetime', 'appointment_date', 'inspection_datetime']),
                'label' => fake()->randomElement(['التاريخ والوقت', 'تاريخ الموعد', 'تاريخ ووقت المعاينة']),
                'placeholder' => 'اختر التاريخ والوقت',
            ],
            'checkbox' => [
                'name' => fake()->randomElement(['terms', 'agreement', 'has_garage', 'has_garden']),
                'label' => fake()->randomElement(['موافق على الشروط', 'اتفاق', 'يوجد جراج', 'يوجد حديقة']),
                'placeholder' => null,
            ],
            'radio' => [
                'name' => fake()->randomElement(['status', 'condition', 'type', 'priority']),
                'label' => fake()->randomElement(['الحالة', 'الحالة العامة', 'النوع', 'الأولوية']),
                'placeholder' => null,
            ],
            'select' => [
                'name' => fake()->randomElement(['category', 'city', 'district', 'condition', 'type', 'status']),
                'label' => fake()->randomElement(['الفئة', 'المدينة', 'المنطقة', 'الحالة', 'النوع', 'الحالة']),
                'placeholder' => 'اختر من القائمة',
            ],
            'textarea' => [
                'name' => fake()->randomElement(['description', 'notes', 'details', 'comments']),
                'label' => fake()->randomElement(['الوصف', 'الملاحظات', 'التفاصيل', 'التعليقات']),
                'placeholder' => fake()->randomElement(['أدخل الوصف', 'أدخل الملاحظات', 'أدخل التفاصيل']),
            ],
            'file' => [
                'name' => fake()->randomElement(['document', 'file', 'attachment']),
                'label' => fake()->randomElement(['المستند', 'الملف', 'المرفق']),
                'placeholder' => null,
            ],
            'image' => [
                'name' => fake()->randomElement(['image', 'photo', 'picture']),
                'label' => fake()->randomElement(['الصورة', 'الصورة الرئيسية', 'صورة']),
                'placeholder' => null,
            ],
            'video' => [
                'name' => 'video',
                'label' => 'الفيديو',
                'placeholder' => null,
            ],
            'audio' => [
                'name' => 'audio',
                'label' => 'الصوت',
                'placeholder' => null,
            ],
        ];

        return $data[$type] ?? $data['text'];
    }

    /**
     * Create a select input (will need options)
     */
    public function select(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'select',
                'name' => fake()->randomElement(['category', 'city', 'district', 'condition', 'type', 'status']),
                'label' => fake()->randomElement(['الفئة', 'المدينة', 'المنطقة', 'الحالة', 'النوع']),
                'placeholder' => 'اختر من القائمة',
            ];
        });
    }

    /**
     * Create a required input
     */
    public function required(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_required' => true,
            ];
        });
    }
}

