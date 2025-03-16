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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            
            // Nowe statusy zamiast starych (declared, paid, confirmed)
            $table->enum('status', ['interested', 'in_talks', 'contract_signed', 'cancelled'])
                  ->default('interested');
            
            // Pola kontaktowe zamiast transaction_reference
            $table->string('contact_preference')->nullable();
            $table->text('contact_details')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
