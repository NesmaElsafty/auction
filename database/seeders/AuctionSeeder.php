<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Category;
use App\Models\ItemData;
use App\Models\Input;
use App\Models\Option;
use App\Models\Screen;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::with(['screens.inputs.options'])->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $users = User::where('type', 'user')->get();
        $agencies = Agency::with('user')->get();

        if ($users->isEmpty() && $agencies->isEmpty()) {
            $this->command->warn('No users or agencies found. Please run UserSeeder and AgencySeeder first.');
            return;
        }

        // Create auctions for each category
        foreach ($categories as $category) {
            // Create 2-5 auctions per category
            $auctionCount = fake()->numberBetween(2, 5);
            
            for ($i = 0; $i < $auctionCount; $i++) {
                // Randomly choose between user or agency
                $useAgency = !$agencies->isEmpty() && fake()->boolean(40); // 40% chance to use agency
                
                if ($useAgency && !$agencies->isEmpty()) {
                    $agency = $agencies->random();
                    $auction = Auction::factory()
                        ->forCategory($category)
                        ->forAgency($agency)
                        ->create();
                } else {
                    $user = $users->random();
                    $auction = Auction::factory()
                        ->forCategory($category)
                        ->forUser($user)
                        ->create();
                }

                // Create itemData for all inputs in all screens of this category
                $this->createItemDataForAuction($auction, $category);
            }
        }

        $this->command->info('Auctions and ItemData created successfully!');
    }

    /**
     * Create itemData for all inputs in all screens of the category
     */
    private function createItemDataForAuction(Auction $auction, Category $category): void
    {
        // Get all screens for this category
        $screens = Screen::where('category_id', $category->id)
            ->with('inputs.options')
            ->get();

        foreach ($screens as $screen) {
            // Get all inputs for this screen
            $inputs = Input::where('screen_id', $screen->id)->get();

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
            }
        }
    }

    /**
     * Generate appropriate value based on input type
     */
    private function generateValueForInput(Input $input): ?string
    {
        return match ($input->type) {
            'text' => fake()->words(3, true),
            'number' => (string) fake()->numberBetween(1, 10000),
            'email' => fake()->email(),
            'password' => fake()->password(8, 16),
            'date' => fake()->date('Y-m-d'),
            'time' => fake()->time('H:i:s'),
            'datetime' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'textarea' => fake()->paragraph(2),
            'checkbox' => fake()->boolean() ? '1' : '0',
            'radio', 'select' => $this->getOptionValue($input),
            'file', 'image', 'video', 'audio' => fake()->optional(0.3)->url(), // 30% chance of having a file URL
            default => fake()->words(2, true),
        };
    }

    /**
     * Get value from options if available, otherwise generate random value
     */
    private function getOptionValue(Input $input): string
    {
        $options = Option::where('input_id', $input->id)->get();
        
        if ($options->isNotEmpty()) {
            // Return random option value
            return $options->random()->value;
        }
        
        // If no options, return a default value based on input name
        return fake()->word();
    }
}

