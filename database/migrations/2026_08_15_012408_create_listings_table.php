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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('type')->default('offer');
            $table->string('title');
            $table->string('product');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('unit', 40)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('is_negotiable')->default(true);
            $table->string('location');
            $table->date('available_from')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['location', 'product']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
