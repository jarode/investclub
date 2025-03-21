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
                
                // Pola ról i weryfikacji użytkownika
                $table->string('role')->default('investor');
                $table->string('verification_status')->default('unverified');
                $table->string('kyc_status')->default('unverified');
                
                // Pole dla statusu subskrypcji Stripe
                $table->string('stripe_subscription_status')->default('inactive');
                
                // Jetstream fields
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();
                
                // New column
                $table->string('verification_session_id')->nullable();
                
                $table->timestamps();
            });
        } else {
            // Jeśli tabela istnieje, dodaj nowe kolumny, jeśli ich nie ma
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->string('role')->default('investor');
                }
                if (!Schema::hasColumn('users', 'verification_status')) {
                    $table->string('verification_status')->default('unverified');
                }
                if (!Schema::hasColumn('users', 'kyc_status')) {
                    $table->string('kyc_status')->default('unverified');
                }
                if (!Schema::hasColumn('users', 'stripe_subscription_status')) {
                    $table->string('stripe_subscription_status')->default('inactive');
                }
                if (!Schema::hasColumn('users', 'verification_session_id')) {
                    $table->string('verification_session_id')->nullable();
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
                if (Schema::hasColumn('users', 'role')) {
                    $table->dropColumn('role');
                }
                if (Schema::hasColumn('users', 'verification_status')) {
                    $table->dropColumn('verification_status');
                }
                if (Schema::hasColumn('users', 'kyc_status')) {
                    $table->dropColumn('kyc_status');
                }
                if (Schema::hasColumn('users', 'stripe_subscription_status')) {
                    $table->dropColumn('stripe_subscription_status');
                }
                if (Schema::hasColumn('users', 'verification_session_id')) {
                    $table->dropColumn('verification_session_id');
                }
            });
        }
        
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
