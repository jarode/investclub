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
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            
            // Dodatkowe kolumny dla Stripe
            $table->string('stripe_subscription_status')->default('inactive');
            $table->string('verification_session_id')->nullable();
            $table->string('kyc_status')->default('pending');
            $table->string('checkout_session_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex([
                'stripe_id',
            ]);

            $table->dropColumn([
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'trial_ends_at',
                'stripe_subscription_status',
                'verification_session_id',
                'kyc_status',
                'checkout_session_id',
                'stripe_customer_id',
                'stripe_subscription_id',
            ]);
        });
    }
};
