<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('Conseils');
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image_path')->nullable();
            $table->string('author_name')->default('Rédaction AgriKoom');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('published'); // published, draft, archived
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('reading_time')->default(5);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('category');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

