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
            // Usuń stare kolumny, jeśli istnieją
            $columnsToRemove = [
                'subscription_status',
                'subscription_type'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Dodaj nowe kolumny
            if (!Schema::hasColumn('users', 'stripe_subscription_status')) {
                $table->string('stripe_subscription_status')->default('inactive');
            }
            if (!Schema::hasColumn('users', 'plan_type')) {
                $table->string('plan_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'cancellation_requested')) {
                $table->boolean('cancellation_requested')->default(false);
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
                'stripe_subscription_status',
                'plan_type',
                'cancellation_requested'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Przywróć stare kolumny
            if (!Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->default('inactive');
            }
            if (!Schema::hasColumn('users', 'subscription_type')) {
                $table->string('subscription_type')->nullable();
            }
        });
    }
};
