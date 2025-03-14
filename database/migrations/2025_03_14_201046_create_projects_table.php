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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('target_amount', 12, 2);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->decimal('min_investment', 12, 2);
            $table->enum('status', ['draft', 'active', 'funded', 'completed'])->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('returns_projection', 5, 2); // Np. 8.50 dla 8.5%
            $table->enum('risk_level', ['low', 'medium', 'high']);
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes(); // Dodajemy możliwość miękkiego usuwania projektów
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
