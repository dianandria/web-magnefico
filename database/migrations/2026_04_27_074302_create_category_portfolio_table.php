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
        Schema::create('category_portfolio', function (Blueprint $table) {
            $table->id();

            // Link to the portfolios table
            $table->foreignId('portfolio_id')
                  ->constrained()
                  ->cascadeOnDelete();
                  
            // Link to the portfolio_categories table
            $table->foreignId('portfolio_category_id')
                  ->constrained('portfolio_categories')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_portfolio');
    }
};
