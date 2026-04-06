
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_ga_history', function (Blueprint $table) {
            $table->id();
            $table->integer('generation');
            $table->json('chromosome')->comment('Representasi kromosom dalam JSON');
            $table->decimal('fitness_score', 10, 2);
            $table->decimal('volume_utilization', 5, 2);
            $table->timestamps();
            
            // Foreign keys
            $table->unsignedBigInteger('packing_id');
            
            // Foreign key constraints
            $table->foreign('packing_id')->references('id')->on('packings')->onDelete('cascade');
            
            $table->index('packing_id');
            $table->index('generation');
            $table->index('fitness_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_ga_history');
    }
};