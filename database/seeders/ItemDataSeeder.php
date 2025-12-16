<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Category;
use App\Models\ItemData;
use App\Models\Input;
use App\Models\Option;
use App\Models\Screen;
use App\Models\Setting;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all auctions with their categories
        $auctions = Auction::with('category')->get();
        
        if ($auctions->isEmpty()) {
            $this->command->warn('No auctions found. Please run AuctionSeeder first.');
            return;
        }

        $itemDataCount = 0;

        // Process each auction
        foreach ($auctions as $auction) {
            // Skip if auction doesn't have a category
            if (!$auction->category) {
                $this->command->warn("Auction #{$auction->id} doesn't have a category. Skipping...");
                continue;
            }

            // Check if ItemData already exists for this auction
            $existingItemData = ItemData::where('auction_id', $auction->id)->count();
            if ($existingItemData > 0) {
                $this->command->info("ItemData already exists for Auction #{$auction->id}. Skipping...");
                continue;
            }

            // Create itemData for all inputs in all screens of this category
            $created = $this->createItemDataForAuction($auction, $auction->category);
            $itemDataCount += $created;
            
            // Create payments if they don't exist for this auction
            $this->createPaymentsForAuction($auction);
        }

        $this->command->info("ItemData seeder completed! Created {$itemDataCount} ItemData records.");
    }

    /**
     * Create payments for auction based on finance settings
     * 
     * @param Auction $auction
     */
    private function createPaymentsForAuction(Auction $auction): void
    {
        // Check if payments already exist for this auction
        $existingPayments = Payment::where('auction_id', $auction->id)->count();
        if ($existingPayments > 0) {
            return; // Payments already exist, skip
        }
        
        $settings = Setting::where('type', 'finance')->get();
        
        if ($settings->isEmpty()) {
            $this->command->warn("No finance settings found. Skipping payments for Auction #{$auction->id}");
            return;
        }
        
        foreach ($settings as $setting) {
            $amount = $setting->value;
            Payment::create([
                'auction_id' => $auction->id,
                'setting_id' => $setting->id,
                'amount' => $amount,
                'is_paid' => false,
                'payment_method' => 'pending',
            ]);
        }
    }

    /**
     * Create itemData for all inputs in all screens of the category
     * 
     * @param Auction $auction
     * @param Category $category
     * @return int Number of ItemData records created
     */
    private function createItemDataForAuction(Auction $auction, Category $category): int
    {
        // Get all screens for this category with their inputs
        $screens = Screen::where('category_id', $category->id)
            ->with('inputs.options')
            ->get();

        if ($screens->isEmpty()) {
            $this->command->warn("Category '{$category->name}' (ID: {$category->id}) doesn't have any screens.");
            return 0;
        }

        $createdCount = 0;

        foreach ($screens as $screen) {
            // Get all inputs for this screen
            $inputs = Input::where('screen_id', $screen->id)->get();

            if ($inputs->isEmpty()) {
                $this->command->warn("Screen '{$screen->title}' (ID: {$screen->id}) doesn't have any inputs.");
                continue;
            }

            foreach ($inputs as $input) {
                // Generate value based on input type
                $value = $this->generateValueForInput($input);

                // Create itemData
                ItemData::create([
                    'auction_id' => $auction->id,
                    'input_id' => $input->id,
                    'label' => $input->label ?? $input->name,
                    'value' => $value,
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} ItemData records for Auction #{$auction->id} (Category: {$category->name})");
        
        return $createdCount;
    }

    /**
     * Generate appropriate value based on input type and label (Arabic)
     * 
     * @param Input $input
     * @return string|null
     */
    private function generateValueForInput(Input $input): ?string
    {
        $label = $input->label ?? $input->name;
        
        // Generate Arabic value based on input label
        $arabicValue = $this->generateArabicValueForLabel($input->type, $label, $input);
        
        if ($arabicValue !== null) {
            return $arabicValue;
        }
        
        // Fallback to type-based generation with Arabic values
        return match ($input->type) {
            'text' => $this->generateArabicText(),
            'number' => (string) fake()->numberBetween(1, 10000),
            'email' => fake()->email(),
            'password' => fake()->password(8, 16),
            'date' => fake()->date('Y-m-d'),
            'time' => fake()->time('H:i:s'),
            'datetime' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'textarea' => $this->generateArabicParagraph(),
            'checkbox' => fake()->boolean() ? 'نعم' : 'لا',
            'radio', 'select' => $this->getOptionValue($input),
            'file', 'image', 'video', 'audio' => fake()->optional(0.3)->url(), // 30% chance of having a file URL
            default => $this->generateArabicText(),
        };
    }
    
    /**
     * Generate Arabic value based on input label
     * 
     * @param string $type
     * @param string $label
     * @param Input $input
     * @return string|null
     */
    private function generateArabicValueForLabel(string $type, string $label, Input $input): ?string
    {
        // Handle select/radio inputs with options
        if (in_array($type, ['select', 'radio'])) {
            return $this->getOptionValue($input);
        }
        
        // Generate Arabic values based on label
        if (str_contains($label, 'رقم اللوحة') || str_contains($label, 'plate_number')) {
            return fake()->numerify('####-###');
        }
        
        if (str_contains($label, 'رقم الهيكل') || str_contains($label, 'chassis_number')) {
            return fake()->bothify('??#########');
        }
        
        if (str_contains($label, 'الرقم التسلسلي') || str_contains($label, 'serial_number')) {
            return fake()->bothify('SN-#######');
        }
        
        if (str_contains($label, 'الكيلومترات') || str_contains($label, 'kilometers')) {
            return (string) fake()->numberBetween(0, 300000);
        }
        
        if (str_contains($label, 'الشركة المصنعة') || str_contains($label, 'manufacturer')) {
            $manufacturers = ['تويوتا', 'نيسان', 'هوندا', 'شيفروليه', 'فورد', 'مرسيدس', 'بي إم دبليو', 'أودي', 'فولكس فاجن', 'هيونداي'];
            return fake()->randomElement($manufacturers);
        }
        
        if (str_contains($label, 'الطراز') || str_contains($label, 'model')) {
            $models = ['كامري', 'أكورد', 'سوناتا', 'ألتima', 'كورولا', 'سيفيك', 'إلنترا', 'أفيون', 'أوبتيما', 'فورت'];
            return fake()->randomElement($models);
        }
        
        if (str_contains($label, 'سنة الصنع') || str_contains($label, 'year')) {
            return (string) fake()->numberBetween(2010, 2024);
        }
        
        if (str_contains($label, 'اللون الخارجي') || str_contains($label, 'exterior_color')) {
            $colors = ['أبيض', 'أسود', 'فضي', 'رمادي', 'أحمر', 'أزرق', 'بني', 'أخضر'];
            return fake()->randomElement($colors);
        }
        
        if (str_contains($label, 'اللون الداخلي') || str_contains($label, 'interior_color')) {
            $colors = ['أسود', 'بيج', 'بني', 'رمادي', 'أبيض'];
            return fake()->randomElement($colors);
        }
        
        if (str_contains($label, 'رقم القطعة') || str_contains($label, 'plot_number')) {
            return fake()->numerify('####');
        }
        
        if (str_contains($label, 'رقم المخطط') || str_contains($label, 'plan_number')) {
            return fake()->bothify('M-####');
        }
        
        if (str_contains($label, 'الرقم المساحي') || str_contains($label, 'survey_number')) {
            return fake()->bothify('SUR-#######');
        }
        
        if (str_contains($label, 'العنوان') || str_contains($label, 'address')) {
            $streets = ['شارع الملك فهد', 'شارع العليا', 'شارع التحلية', 'شارع الأمير سلطان', 'شارع العروبة', 'شارع الجامعة'];
            $numbers = fake()->numberBetween(1, 9999);
            return fake()->randomElement($streets) . '، رقم ' . $numbers;
        }
        
        if (str_contains($label, 'المساحة') || str_contains($label, 'area')) {
            return (string) fake()->numberBetween(100, 5000);
        }
        
        if (str_contains($label, 'الطول') || str_contains($label, 'length')) {
            return (string) fake()->numberBetween(10, 100);
        }
        
        if (str_contains($label, 'العرض') || str_contains($label, 'width')) {
            return (string) fake()->numberBetween(10, 100);
        }
        
        if (str_contains($label, 'عدد الطوابق') || str_contains($label, 'floors')) {
            return (string) fake()->numberBetween(1, 5);
        }
        
        if (str_contains($label, 'عدد الوحدات') || str_contains($label, 'units')) {
            return (string) fake()->numberBetween(1, 20);
        }
        
        if (str_contains($label, 'موقع المعاينة') || str_contains($label, 'inspection_location')) {
            $locations = ['الرياض، حي العليا', 'جدة، الكورنيش', 'الدمام، الفناتير', 'الرياض، الملقا', 'جدة، الزهراء'];
            return fake()->randomElement($locations);
        }
        
        if (str_contains($label, 'وقت المعاينة') || str_contains($label, 'inspection_time')) {
            return fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d H:i:s');
        }
        
        // For text inputs with Arabic labels, generate Arabic text
        if ($type === 'text' && preg_match('/[\x{0600}-\x{06FF}]/u', $label)) {
            $arabicTexts = [
                'معلومات إضافية عن القطعة',
                'تفاصيل إضافية مهمة',
                'ملاحظات خاصة',
                'معلومات تكميلية',
                'تفاصيل إضافية',
            ];
            return fake()->randomElement($arabicTexts);
        }
        
        // For textarea with Arabic labels
        if ($type === 'textarea' && preg_match('/[\x{0600}-\x{06FF}]/u', $label)) {
            $arabicParagraphs = [
                'هذه معلومات تفصيلية عن القطعة المعروضة في المزاد. القطعة في حالة ممتازة وتتمتع بجميع المواصفات المطلوبة.',
                'تفاصيل إضافية مهمة يجب الاطلاع عليها قبل المشاركة في المزاد. جميع المعلومات دقيقة ومحدثة.',
                'ملاحظات خاصة عن القطعة المعروضة. يرجى مراجعة جميع التفاصيل بعناية قبل تقديم العطاء.',
            ];
            return fake()->randomElement($arabicParagraphs);
        }
        
        return null;
    }

    /**
     * Get value from options if available, otherwise generate random value
     * 
     * @param Input $input
     * @return string
     */
    private function getOptionValue(Input $input): string
    {
        $options = Option::where('input_id', $input->id)->get();
        
        if ($options->isNotEmpty()) {
            // Return random option label (Arabic) if available, otherwise value
            $option = $options->random();
            return $option->label ?? $option->value;
        }
        
        // If no options, return a default Arabic value
        return $this->generateArabicText();
    }
    
    /**
     * Generate Arabic text
     * 
     * @return string
     */
    private function generateArabicText(): string
    {
        $arabicTexts = [
            'معلومات إضافية',
            'تفاصيل مهمة',
            'ملاحظات خاصة',
            'معلومات تكميلية',
            'بيانات إضافية',
            'تفاصيل إضافية',
            'معلومات تفصيلية',
            'بيانات مهمة',
        ];
        return fake()->randomElement($arabicTexts);
    }
    
    /**
     * Generate Arabic paragraph
     * 
     * @return string
     */
    private function generateArabicParagraph(): string
    {
        $arabicParagraphs = [
            'هذه معلومات تفصيلية عن القطعة المعروضة في المزاد. القطعة في حالة ممتازة وتتمتع بجميع المواصفات المطلوبة.',
            'تفاصيل إضافية مهمة يجب الاطلاع عليها قبل المشاركة في المزاد. جميع المعلومات دقيقة ومحدثة.',
            'ملاحظات خاصة عن القطعة المعروضة. يرجى مراجعة جميع التفاصيل بعناية قبل تقديم العطاء.',
            'معلومات شاملة عن القطعة تتضمن جميع التفاصيل المهمة. القطعة جاهزة للاستخدام الفوري.',
            'بيانات تفصيلية دقيقة عن القطعة المعروضة. جميع المواصفات والمعلومات متوفرة ومؤكدة.',
        ];
        return fake()->randomElement($arabicParagraphs);
    }
}

