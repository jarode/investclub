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
            // Dodajemy kolumny tylko jeśli nie istnieją
            if (!Schema::hasColumn('users', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index();
            }
            
            if (!Schema::hasColumn('users', 'pm_type')) {
                $table->string('pm_type')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable();
            }
            
            if (!Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
            
            // Nie dodajemy stripe_subscription_status i kyc_status, bo już istnieją w tabeli
            
            // Dodatkowe kolumny tylko jeśli ich brakuje
            if (!Schema::hasColumn('users', 'verification_session_id')) {
                $table->string('verification_session_id')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'checkout_session_id')) {
                $table->string('checkout_session_id')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Usuwamy indeks tylko jeśli kolumna istnieje
            if (Schema::hasColumn('users', 'stripe_id')) {
                $table->dropIndex(['stripe_id']);
            }

            // Usuwamy kolumny tylko jeśli istnieją
            $columns = [
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'trial_ends_at',
                'verification_session_id',
                'checkout_session_id',
                'stripe_customer_id',
                'stripe_subscription_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
