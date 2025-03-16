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
        Schema::table('investments', function (Blueprint $table) {
            $table->string('contact_preference')->nullable();
            $table->text('contact_details')->nullable();
            $table->dropColumn('transaction_reference');
            
            // Najpierw usuwamy stary enum
            $table->dropColumn('status');
            
            // Dodajemy nowy enum ze zaktualizowanymi statusami
            $table->enum('status', ['interested', 'in_talks', 'contract_signed', 'cancelled'])
                  ->default('interested')
                  ->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['contact_preference', 'contact_details']);
            $table->string('transaction_reference')->nullable();
            
            // Przywracamy stary enum
            $table->dropColumn('status');
            $table->enum('status', ['declared', 'paid', 'confirmed', 'cancelled'])
                  ->default('declared')
                  ->after('amount');
        });
    }
};
