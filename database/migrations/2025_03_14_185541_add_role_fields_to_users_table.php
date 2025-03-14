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
            $table->string('role')->default('investor')->after('remember_token');
            $table->string('verification_status')->default('unverified')->after('role');
            $table->decimal('wallet_balance', 10, 2)->default(0)->after('verification_status');
            $table->string('kyc_status')->default('pending')->after('wallet_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'verification_status', 'wallet_balance', 'kyc_status']);
        });
    }
};
