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
            if (!Schema::hasColumn('users', 'verification_session_id')) {
                $table->string('verification_session_id')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->string('kyc_status')->default('unverified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'verification_session_id')) {
                $table->dropColumn('verification_session_id');
            }
        });
    }
};
