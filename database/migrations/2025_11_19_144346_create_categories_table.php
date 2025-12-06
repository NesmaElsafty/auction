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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->decimal('start_precentage', 10, 2)->nullable();
            $table->decimal('end_precentage', 10, 2)->nullable();

            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->enum('type', ['terms', 'contracts']);

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
