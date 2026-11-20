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
        Schema::table('market_prices', function (Blueprint $table) {
            $table->decimal('change_percent', 5, 2)->default(0.0)->nullable()->after('price_range');
            $table->string('previous_price')->nullable()->after('change_percent');
            $table->string('ticker_code', 20)->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_prices', function (Blueprint $table) {
            $table->dropColumn(['change_percent', 'previous_price', 'ticker_code']);
        });
    }
};
