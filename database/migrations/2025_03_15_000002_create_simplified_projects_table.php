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
            $table->decimal('min_investment', 12, 2);
            $table->string('status')->default('draft');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('returns_projection', 5, 2);
            $table->string('risk_level');
            $table->string('category');
            $table->string('location');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
