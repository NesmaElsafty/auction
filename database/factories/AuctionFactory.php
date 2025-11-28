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
        
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->dateTimeBetween($startDate, '+60 days');
        
        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'user_type' => User::class,
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'type' => fake()->randomElement($auctionTypes),
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

