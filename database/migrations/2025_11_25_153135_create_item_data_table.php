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
        Schema::create('item_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->onDelete('cascade');
            $table->foreignId('input_id')->constrained('inputs')->onDelete('cascade');
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
