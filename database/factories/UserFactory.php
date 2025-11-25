<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arabicFirstNames = [
            'محمد', 'أحمد', 'علي', 'خالد', 'عبدالله', 'فهد', 'سعود', 'عبدالرحمن',
            'عمر', 'يوسف', 'حمد', 'سلطان', 'مشعل', 'تركي', 'نايف', 'بندر',
            'فاطمة', 'سارة', 'نورة', 'مريم', 'لينا', 'ريم', 'هند', 'خلود',
            'أمل', 'نورا', 'لولوة', 'منى', 'عائشة', 'خديجة', 'زينب', 'مها'
        ];

        $arabicLastNames = [
            'الزيد', 'المالكي', 'العتيبي', 'الدوسري', 'الشمري', 'الغامدي',
            'الحربي', 'القحطاني', 'العنزي', 'الخالدي', 'السالم', 'الراشد',
            'الخليفي', 'الجبير', 'النجدي', 'القرشي', 'التميمي', 'السهلي'
        ];

        $saudiCities = [
            'الرياض', 'جدة', 'الدمام', 'المدينة المنورة', 'مكة المكرمة',
            'الطائف', 'بريدة', 'خميس مشيط', 'حائل', 'الجبيل',
            'نجران', 'أبها', 'جازان', 'الخبر', 'تبوك'
        ];

        $saudiAddresses = [
            'طريق الملك فهد', 'طريق الملك عبدالعزيز', 'طريق الملك خالد',
            'طريق الملك سعود', 'طريق الأمير سلطان', 'طريق الأمير محمد بن سلمان',
            'حي النرجس', 'حي العليا', 'حي المطار', 'حي العليا', 'حي الياسمين',
            'حي الروابي', 'حي النزهة', 'حي الصفا', 'حي الزهراء', 'حي الورود'
        ];

        $arabicSummaries = [
            'مشارك نشط في المزادات مع خبرة واسعة في مجال المزادات الإلكترونية.',
            'مستثمر ومشارك في المزادات العامة والخاصة منذ أكثر من 10 سنوات.',
            'أهتم بالمزادات الفنية والتحف وأبحث عن القطع النادرة.',
            'مشارك في المزادات العقارية والسيارات مع سجل حافل بالنجاحات.',
            'أهتم بالمزادات الإلكترونية وأسعى للحصول على أفضل الصفقات.',
            'مستثمر في المزادات مع خبرة في تقييم القطع والتحف.',
            'مشارك نشط في المزادات العامة وأبحث عن الفرص الاستثمارية.',
            'أهتم بالمزادات الفنية والثقافية وأجمع القطع الأثرية.',
            'مستثمر في المزادات العقارية والسيارات مع خبرة عملية.',
            'مشارك في المزادات الإلكترونية مع سجل ممتاز في الصفقات.',
        ];

        $saudiBanks = [
            'البنك الأهلي السعودي',
            'البنك السعودي الفرنسي',
            'بنك الرياض',
            'البنك السعودي للاستثمار',
            'بنك الجزيرة',
            'البنك السعودي الهولندي',
            'بنك الراجحي',
            'البنك العربي الوطني',
            'بنك ساب',
            'البنك السعودي البريطاني',
            'بنك الإمارات دبي الوطني',
            'البنك الأول',
            'بنك الخليج',
            'بنك الكويت الوطني',
        ];

        $firstName = fake()->randomElement($arabicFirstNames);
        $lastName = fake()->randomElement($arabicLastNames);
        $city = fake()->randomElement($saudiCities);
        $street = fake()->randomElement($saudiAddresses);
        $buildingNumber = fake()->numberBetween(1, 9999);
        $bankName = fake()->randomElement($saudiBanks);
        $bankAccountName = $firstName . ' ' . $lastName;
        $bankAccountNumber = fake()->numerify('##########');
        
        // Generate Saudi IBAN (SA + 2 check digits + 20 alphanumeric)
        $iban = 'SA' . fake()->numerify('##') . fake()->bothify('####################');
        
        // Generate SWIFT code (4 letters + 2 letters + 2 alphanumeric + optional 3 alphanumeric)
        $swift = strtoupper(fake()->bothify('??????##'));

        return [
            'name' => $firstName . ' ' . $lastName,
            'national_id' => fake()->unique()->numerify('##########'),
            'email' => 'user' . fake()->unique()->numerify('######') . '@example.com',
            'phone' => '+966' . fake()->numerify('#########'),
            'address' => $street . '، ' . $city . '، المملكة العربية السعودية - مبنى رقم ' . $buildingNumber,
            'summary' => fake()->randomElement($arabicSummaries),
            'link' => fake()->url(),
            'password' => static::$password ??= Hash::make('password'),
            'type' => 'user',
            'is_active' => true,
            'remember_token' => Str::random(10),
            // Bank data
            'bank_name' => $bankName,
            'bank_account_name' => $bankAccountName,
            'bank_account_number' => $bankAccountNumber,
            'bank_address' => $street . '، ' . $city . '، المملكة العربية السعودية',
            'IBAN' => $iban,
            'SWIFT' => $swift,
        ];
    }

    /**
     * Indicate that the user should be an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'admin',
        ]);
    }
}
