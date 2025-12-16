<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $auctionTypes = ['online', 'both'];
        $statuses = ['pending', 'current', 'completed', 'cancelled'];
        $postTypes = ['auction', 'purchase', 'demolition'];
        
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->dateTimeBetween($startDate, '+60 days');
        // Viewing date should be before start date
        $viewingDate = fake()->optional(0.7)->dateTimeBetween('now', $startDate); // 70% chance of having viewing date
        
        // Generate Saudi Arabia location data
        $locationData = $this->generateSaudiArabiaLocation();
        
        // Arabic auction names
        $arabicNames = [
            'مزاد علني على أرض سكنية',
            'مزاد علني على سيارة فاخرة',
            'مزاد علني على عقار تجاري',
            'مزاد علني على منزل',
            'مزاد علني على فيلا',
            'مزاد علني على شقة',
            'مزاد علني على قطعة أرض',
            'مزاد علني على مركبة',
            'مزاد علني على معدات',
            'مزاد علني على أثاث',
        ];
        
        // Arabic descriptions
        $arabicDescriptions = [
            'مزاد علني على قطعة أرض سكنية بموقع مميز في قلب المدينة. القطعة جاهزة للبناء وتتمتع بمواصفات ممتازة وإطلالة رائعة.',
            'مزاد علني على سيارة فاخرة بحالة ممتازة. المركبة خالية من الحوادث وصيانة دورية في الوكالة المعتمدة.',
            'مزاد علني على عقار تجاري في موقع استراتيجي. العقار مناسب للمشاريع التجارية والاستثمارية.',
            'مزاد علني على منزل راقي بمساحة واسعة. المنزل يحتوي على جميع المرافق والخدمات الحديثة.',
            'مزاد علني على فيلا فاخرة بتصميم عصري. الفيلا تتمتع بحديقة واسعة ومواقف للسيارات.',
            'مزاد علني على شقة في مجمع سكني راقي. الشقة مطلة على البحر وتتمتع بجميع الخدمات.',
            'مزاد علني على قطعة أرض بمساحة كبيرة. القطعة صالحة للبناء الفوري وتتمتع بجميع الخدمات.',
            'مزاد علني على مركبة بحالة جيدة جداً. المركبة خضعت لصيانة دورية ومستنداتها كاملة.',
            'مزاد علني على معدات صناعية حديثة. المعدات بحالة ممتازة وجاهزة للاستخدام الفوري.',
            'مزاد علني على أثاث فاخر ومستعمل بحالة جيدة. الأثاث من أجود الأنواع والمحافظة عليه ممتازة.',
        ];
        
        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'user_type' => User::class,
            'name' => fake()->randomElement($arabicNames),
            'description' => fake()->randomElement($arabicDescriptions),
            'type' => fake()->randomElement($auctionTypes),
            'post_type' => fake()->randomElement($postTypes),
            'is_infaz' => fake()->boolean(30), // 30% chance of being true
            'start_price' => fake()->randomFloat(2, 1000, 100000),
            'end_price' => fake()->randomFloat(2, 100000, 1000000),
            'deposit_price' => fake()->randomFloat(2, 5000, 50000),
            'minimum_bid_increment' => fake()->numberBetween(100, 5000),
            'youtube_link' => fake()->optional(0.5)->url(), // 50% chance of having a YouTube link
            'start_date' => $startDate,
            'end_date' => $endDate,
            'awarding_period_days' => fake()->numberBetween(1, 30),
            'status' => fake()->randomElement($statuses),
            'is_active' => true,
            'is_approved' => fake()->boolean(70), // 70% chance of being approved
            'location' => $locationData['location'],
            'lat' => $locationData['lat'],
            'long' => $locationData['long'],
            'viewing_date' => $viewingDate,
        ];
    }
    
    /**
     * Generate Saudi Arabia location data
     */
    private function generateSaudiArabiaLocation(): array
    {
        // Major Saudi cities with their coordinates and districts
        $saudiCities = [
            [
                'city' => 'الرياض',
                'districts' => ['العليا', 'الملز', 'النخيل', 'الورود', 'المرسلات', 'الروضة', 'الملقا', 'العريجاء'],
                'lat' => 24.7136,
                'long' => 46.6753,
                'latRange' => [24.5, 25.0],
                'longRange' => [46.5, 47.0],
            ],
            [
                'city' => 'جدة',
                'districts' => ['الكورنيش', 'الزهراء', 'الروابي', 'السلامة', 'البغدادية', 'الخالدية', 'الفيصلية', 'المنتزهات'],
                'lat' => 21.4858,
                'long' => 39.1925,
                'latRange' => [21.3, 21.7],
                'longRange' => [39.0, 39.4],
            ],
            [
                'city' => 'الدمام',
                'districts' => ['الكورنيش', 'الفناتير', 'الفيصلية', 'الخليج', 'المنطقة الصناعية', 'الروضة'],
                'lat' => 26.4207,
                'long' => 50.0888,
                'latRange' => [26.2, 26.6],
                'longRange' => [49.9, 50.3],
            ],
            [
                'city' => 'مكة المكرمة',
                'districts' => ['العزيزية', 'الزاهر', 'الشرائع', 'العوالي', 'الجميزة'],
                'lat' => 21.3891,
                'long' => 39.8579,
                'latRange' => [21.2, 21.6],
                'longRange' => [39.7, 40.1],
            ],
            [
                'city' => 'المدينة المنورة',
                'districts' => ['قباء', 'العوالي', 'العيون', 'المناخة', 'المنطقة المركزية'],
                'lat' => 24.5247,
                'long' => 39.5692,
                'latRange' => [24.4, 24.7],
                'longRange' => [39.4, 39.8],
            ],
            [
                'city' => 'الطائف',
                'districts' => ['الشهداء', 'العزيزية', 'الروضة', 'الحوية', 'الشفا'],
                'lat' => 21.2703,
                'long' => 40.4158,
                'latRange' => [21.1, 21.5],
                'longRange' => [40.2, 40.6],
            ],
            [
                'city' => 'الخبر',
                'districts' => ['الكورنيش', 'الروضة', 'الفيصلية', 'المنطقة الشمالية', 'المنطقة الجنوبية'],
                'lat' => 26.2794,
                'long' => 50.2080,
                'latRange' => [26.1, 26.5],
                'longRange' => [50.0, 50.4],
            ],
            [
                'city' => 'بريدة',
                'districts' => ['الوسط', 'الروضة', 'العزيزية', 'المنطقة الشمالية'],
                'lat' => 26.3260,
                'long' => 43.9750,
                'latRange' => [26.2, 26.5],
                'longRange' => [43.8, 44.2],
            ],
        ];
        
        $selectedCity = fake()->randomElement($saudiCities);
        $district = fake()->randomElement($selectedCity['districts']);
        
        // Generate coordinates within the city range with slight variation
        $lat = fake()->randomFloat(8, $selectedCity['latRange'][0], $selectedCity['latRange'][1]);
        $long = fake()->randomFloat(8, $selectedCity['longRange'][0], $selectedCity['longRange'][1]);
        
        $location = "{$selectedCity['city']}، حي {$district}، المملكة العربية السعودية";
        
        return [
            'location' => $location,
            'lat' => $lat,
            'long' => $long,
        ];
    }

    /**
     * Create auction for a specific category
     */
    public function forCategory(Category $category): static
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category_id' => $category->id,
            ];
        });
    }

    /**
     * Create auction for a specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
                'user_type' => User::class,
            ];
        });
    }

    /**
     * Create auction for a specific agency
     */
    public function forAgency(Agency $agency): static
    {
        return $this->state(function (array $attributes) use ($agency) {
            return [
                'user_id' => $agency->id,
                'user_type' => Agency::class,
            ];
        });
    }
}

