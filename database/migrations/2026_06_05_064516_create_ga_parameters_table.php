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
        Schema::create('ga_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('population_size')->default(100);
            $table->integer('generation_limit')->default(500);
            $table->float('crossover_rate')->default(0.8);
            $table->float('mutation_rate')->default(0.1);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ga_parameters');
    }
};
