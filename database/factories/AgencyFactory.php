<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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

        $agencyTypes = [
            'وكالة عقارية',
            'وكالة مزادات',
            'وكالة استثمارية',
            'وكالة تجارية',
            'وكالة تسويقية',
            'وكالة استشارية',
        ];

        $saudiCities = [
            'الرياض', 'جدة', 'الدمام', 'المدينة المنورة', 'مكة المكرمة',
            'الطائف', 'بريدة', 'خميس مشيط', 'حائل', 'الجبيل',
            'نجران', 'أبها', 'جازان', 'الخبر', 'تبوك'
        ];

        $saudiAddresses = [
            'طريق الملك فهد', 'طريق الملك عبدالعزيز', 'طريق الملك خالد',
            'طريق الملك سعود', 'طريق الأمير سلطان', 'طريق الأمير محمد بن سلمان',
            'حي النرجس', 'حي العليا', 'حي المطار', 'حي الياسمين',
            'حي الروابي', 'حي النزهة', 'حي الصفا', 'حي الزهراء', 'حي الورود'
        ];

        // Generate unique agency name
        // Agency type can repeat, but combination with company name and unique number ensures uniqueness
        $agencyName = fake()->randomElement($agencyTypes) . ' ' . fake()->company() . ' ' . fake()->unique()->numerify('######');
        $city = fake()->randomElement($saudiCities);
        $street = fake()->randomElement($saudiAddresses);
        $buildingNumber = fake()->numberBetween(1, 9999);
        $bankName = fake()->randomElement($saudiBanks);
        $bankAccountName = $agencyName;
        $bankAccountNumber = fake()->numerify('##########');
        
        // Generate Saudi IBAN (SA + 2 check digits + 20 alphanumeric)
        $iban = 'SA' . fake()->numerify('##') . fake()->bothify('####################');
        
        // Generate SWIFT code (4 letters + 2 letters + 2 alphanumeric + optional 3 alphanumeric)
        $swift = strtoupper(fake()->bothify('??????##'));

        // Generate unique agency number (format: AG-YYYY-XXXXXX)
        $agencyNumber = 'AG-' . date('Y') . '-' . fake()->unique()->numerify('######');

        // Generate random date within last 5 years
        $date = fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d');

        return [
            'user_id' => User::factory(),
            'name' => $agencyName,
            'number' => $agencyNumber,
            'date' => $date,
            'address' => $street . '، ' . $city . '، المملكة العربية السعودية - مبنى رقم ' . $buildingNumber,
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
     * Create agency for a specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}

