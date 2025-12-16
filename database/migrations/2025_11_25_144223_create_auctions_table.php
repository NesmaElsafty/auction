<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('auctions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');

            // user_id + user_type
            $table->morphs('user');

            $table->enum('post_type', ['auction', 'purchase', 'demolition'])->nullable();

            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('type', ['online', 'both'])->nullable();
            $table->boolean('is_infaz')->default(false);

            // prices
            $table->decimal('purchase_min_amount', 12, 2)->nullable();
            $table->decimal('purchase_amount', 12, 2)->nullable();


            $table->decimal('start_price', 12, 2)->nullable();
            $table->decimal('end_price', 12, 2)->nullable();
            $table->decimal('deposit_price', 12, 2)->nullable();

            // bidding step
            $table->integer('minimum_bid_increment')->nullable();

            // media
            $table->string('youtube_link')->nullable();

            // timings
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('awarding_period_days')->nullable();

            // location
            $table->text('location')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('long', 11, 8)->nullable();
            $table->timestamp('viewing_date')->nullable();

            // status
            $table->enum('status', ['pending', 'current', 'completed', 'cancelled'])
                ->default('pending');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
