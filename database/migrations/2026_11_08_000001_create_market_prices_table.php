<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('category')->default('Autre');
            $table->string('price_range');
            $table->string('unit')->nullable();
            $table->string('location')->nullable();
            $table->string('trend')->default('stable'); // stable, up, down, active, available, variable
            $table->string('trend_label')->default('Stable');
            $table->string('source')->default('Marchés régionaux & coopératives');
            $table->string('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};

