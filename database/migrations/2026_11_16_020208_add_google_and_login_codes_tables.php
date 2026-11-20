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
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('id');
            }
            // Rendre le password nullable pour les utilisateurs se connectant via Google ou OTP
            $table->string('password')->nullable()->change();
        });

        if (! Schema::hasTable('login_codes')) {
            Schema::create('login_codes', function (Blueprint $table) {
                $table->id();
                $table->string('email')->index();
                $table->string('code', 10);
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('login_codes')) {
            Schema::dropIfExists('login_codes');
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
};
