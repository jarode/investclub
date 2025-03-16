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
            if (!Schema::hasColumn('users', 'plan_type')) {
                $table->string('plan_type')->nullable()->after('stripe_subscription_status');
            }
            
            if (!Schema::hasColumn('users', 'cancellation_requested')) {
                $table->boolean('cancellation_requested')->default(false)->after('plan_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['plan_type', 'cancellation_requested']);
        });
    }
};
