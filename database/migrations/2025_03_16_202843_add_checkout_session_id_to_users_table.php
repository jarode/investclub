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
            // Dodajemy checkout_session_id tylko jeśli nie istnieje
            if (!Schema::hasColumn('users', 'checkout_session_id')) {
                $table->string('checkout_session_id')->nullable();
            }
            
            // Sprawdź, czy istnieją inne wymagane kolumny, jeśli nie, to je dodaj
            if (!Schema::hasColumn('users', 'stripe_subscription_status')) {
                $table->string('stripe_subscription_status')->default('inactive');
            }
            
            if (!Schema::hasColumn('users', 'verification_session_id')) {
                $table->string('verification_session_id')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status')->default('pending');
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
            $table->dropColumn('checkout_session_id');
            // Nie usuwamy innych kolumn, które mogły już istnieć
        });
    }
};
