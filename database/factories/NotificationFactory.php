<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arabicTitles = [
            'إشعار جديد للمزاد',
            'تذكير بموعد المزاد',
            'إشعار بقبول العرض',
            'تذكير بالدفع',
            'إشعار برفض العرض',
            'تذكير بإنتهاء المزاد',
            'إشعار بتحديث المزاد',
            'تذكير بتسجيل الدخول',
            'إشعار بتغيير الحالة',
            'تذكير بتحديث الملف الشخصي',
        ];

        $arabicDescriptions = [
            'تم إنشاء مزاد جديد قد يهمك. يرجى مراجعة التفاصيل والمشاركة إذا كان مناسباً لك.',
            'تذكير: المزاد الذي تتابعه سينتهي قريباً. تأكد من تقديم عرضك قبل انتهاء الوقت المحدد.',
            'تهانينا! تم قبول عرضك في المزاد. يرجى متابعة الخطوات التالية لإتمام المعاملة.',
            'تذكير: يرجى إتمام عملية الدفع للمزاد الفائز به في أقرب وقت ممكن.',
            'نأسف لإبلاغك بأن عرضك في المزاد لم يتم قبوله. يمكنك المحاولة مرة أخرى في مزادات أخرى.',
            'تذكير: المزاد الذي تتابعه سينتهي خلال ساعات قليلة. تأكد من تقديم أفضل عرض لديك.',
            'تم تحديث معلومات المزاد الذي تتابعه. يرجى مراجعة التحديثات الجديدة.',
            'تذكير: لم تقم بتسجيل الدخول منذ فترة. يرجى تسجيل الدخول لمتابعة مزاداتك.',
            'تم تغيير حالة المزاد الذي تتابعه. يرجى مراجعة الحالة الجديدة.',
            'تذكير: يرجى تحديث معلومات ملفك الشخصي للحصول على أفضل تجربة استخدام.',
        ];

        return [
            'title' => fake()->unique()->randomElement($arabicTitles),
            'description' => fake()->unique()->randomElement($arabicDescriptions),
            'type' => fake()->randomElement(['notify', 'reminder']),
            'status' => fake()->randomElement(['pending', 'sent', 'unsent']),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }
}

