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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('location')->nullable()->after('phone');
            $table->string('actor_type')->default('buyer')->after('location');
            $table->text('description')->nullable()->after('actor_type');
            $table->string('avatar_path')->nullable()->after('description');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->boolean('is_admin')->default(false)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'location',
                'actor_type',
                'description',
                'avatar_path',
                'phone_verified_at',
                'is_admin',
            ]);
        });
    }
};
