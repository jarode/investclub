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
            // Usuń indeks na stripe_id, jeśli istnieje
            if (Schema::hasColumn('users', 'stripe_id')) {
                $table->dropIndex('users_stripe_id_index');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Usuń stare kolumny, jeśli istnieją
            $columnsToRemove = [
                'verification_status',
                'kyc_status',
                'stripe_subscription_status',
                'stripe_id',
                'plan_type',
                'cancellation_requested',
                'verification_session_id'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Dodaj nowe kolumny, jeśli nie istnieją
            if (!Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->default('inactive')->after('password');
            }
            if (!Schema::hasColumn('users', 'subscription_type')) {
                $table->string('subscription_type')->nullable()->after('subscription_status');
            }
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('subscription_type');
            }
            
            // Zmień domyślną wartość roli
            if (Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Usuń nowe kolumny
            $columnsToRemove = [
                'subscription_status',
                'subscription_type',
                'is_verified'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Przywróć stare kolumny
            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->string('verification_status')->default('unverified');
            }
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status')->default('unverified');
            }
            if (!Schema::hasColumn('users', 'stripe_subscription_status')) {
                $table->string('stripe_subscription_status')->default('inactive');
            }
            if (!Schema::hasColumn('users', 'stripe_id')) {
                $table->string('stripe_id')->nullable();
                $table->index('stripe_id');
            }
            if (!Schema::hasColumn('users', 'plan_type')) {
                $table->string('plan_type')->default('free');
            }
            if (!Schema::hasColumn('users', 'cancellation_requested')) {
                $table->boolean('cancellation_requested')->default(false);
            }
            if (!Schema::hasColumn('users', 'verification_session_id')) {
                $table->string('verification_session_id')->nullable();
            }
            
            // Przywróć starą domyślną wartość roli
            if (Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('investor')->change();
            }
        });
    }
};
