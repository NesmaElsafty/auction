<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arabicNames = [
            'الاراضي',
            'العقارات',
            'السيارات',
            'المنزلية',
            'الهدم'
        ];

        $arabicTitles = [
            'الشروط والأحكام العامة للمزادات',
            'عقود المزادات العلنية',
            'الشروط الخاصة بالمزادات الإلكترونية',
            'الاتفاقيات التجارية للمزادات',
            'البنود القانونية للعقود',
            'الشروط التعاقدية للمشاركين',
            'الاتفاقيات المالية للمزادات',
            'الشروط العامة للمزادات',
            'عقود بيع المزادات',
            'الشروط الخاصة بالمزادات',
        ];

        $arabicContent = [
            'هذه الشروط والأحكام تحكم استخدام منصة المزادات. يجب على جميع المستخدمين الالتزام بهذه الشروط عند المشاركة في أي مزاد. نحن نحتفظ بالحق في تعديل هذه الشروط في أي وقت دون إشعار مسبق.',
            'تحدد هذه الاتفاقية الشروط والأحكام الخاصة بالمزادات الإلكترونية. يجب على جميع المشاركين قراءة وفهم هذه الشروط قبل المشاركة. أي مخالفة لهذه الشروط قد تؤدي إلى إلغاء المشاركة.',
            'هذه البنود التعاقدية تنطبق على جميع المعاملات التي تتم من خلال منصة المزادات. نحن ملتزمون بحماية حقوق جميع الأطراف المشاركة في المزادات.',
            'تحدد هذه الشروط العلاقة التعاقدية بين المنصة والمستخدمين. يجب على جميع المستخدمين الالتزام بهذه الشروط للحفاظ على نزاهة المزادات.',
            'هذه الاتفاقية تحكم جميع المعاملات المالية المرتبطة بالمزادات. نحن نضمن الشفافية والأمان في جميع المعاملات.',
        ];

        return [
            'name' => fake()->unique()->randomElement($arabicNames),
            'title' => fake()->unique()->randomElement($arabicTitles),
            'content' => fake()->unique()->randomElement($arabicContent),
            'type' => fake()->randomElement(['terms', 'contracts']),
        ];
    }
}
