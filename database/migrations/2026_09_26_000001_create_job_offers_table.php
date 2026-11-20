<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->string('contract_type')->nullable();
            $table->date('deadline')->nullable();
            $table->text('summary');
            $table->text('details')->nullable();
            $table->text('profile')->nullable();
            $table->string('apply_email')->nullable();
            $table->text('apply_url')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['location', 'deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
