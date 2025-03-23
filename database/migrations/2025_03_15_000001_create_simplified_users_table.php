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
        // Sprawdź, czy tabela users już istnieje
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                
                // Two-factor authentication fields
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
                
                $table->rememberToken();
                
                // Uproszczone pola dla subskrypcji i dostępu
                $table->string('role')->default('user'); // Tylko 'user' lub 'admin'
                $table->string('stripe_customer_id')->nullable();
                $table->string('stripe_subscription_id')->nullable();
                $table->string('subscription_status')->default('inactive'); // 'inactive', 'active', 'past_due'
                $table->string('subscription_type')->nullable(); // 'investor', 'owner'
                $table->boolean('is_verified')->default(false); // Zastępuje kyc_status
                
                // Jetstream fields
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();
                
                $table->timestamps();
            });
        } else {
            // Jeśli tabela istnieje, dodaj nowe kolumny i usuń stare
            Schema::table('users', function (Blueprint $table) {
                // Usuń stare kolumny
                $table->dropColumn([
                    'verification_status',
                    'kyc_status',
                    'stripe_subscription_status',
                    'stripe_id',
                    'plan_type',
                    'cancellation_requested',
                    'verification_session_id'
                ]);
                
                // Dodaj nowe kolumny
                if (!Schema::hasColumn('users', 'subscription_status')) {
                    $table->string('subscription_status')->default('inactive');
                }
                if (!Schema::hasColumn('users', 'subscription_type')) {
                    $table->string('subscription_type')->nullable();
                }
                if (!Schema::hasColumn('users', 'is_verified')) {
                    $table->boolean('is_verified')->default(false);
                }
            });
        }

        // Sprawdź, czy tabela password_reset_tokens już istnieje
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Sprawdź, czy tabela sessions już istnieje
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nie usuwamy tabeli users, tylko dodane kolumny
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'subscription_status')) {
                    $table->dropColumn('subscription_status');
                }
                if (Schema::hasColumn('users', 'subscription_type')) {
                    $table->dropColumn('subscription_type');
                }
                if (Schema::hasColumn('users', 'is_verified')) {
                    $table->dropColumn('is_verified');
                }
            });
        }
        
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
