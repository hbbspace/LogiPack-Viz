<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_packages', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_placed')->default(true)->comment('Apakah paket berhasil ditempatkan');
            $table->integer('position_x')->nullable()->comment('Posisi X dalam container (cm)');
            $table->integer('position_y')->nullable()->comment('Posisi Y dalam container (cm)');
            $table->integer('position_z')->nullable()->comment('Posisi Z dalam container (cm)');
            $table->integer('orientation')->nullable()->comment('Orientasi paket (1-6)');
            $table->timestamps();
            
            // Foreign keys
            $table->unsignedBigInteger('packing_id');
            $table->unsignedBigInteger('package_id');
            
            // Foreign key constraints
            $table->foreign('packing_id')->references('id')->on('packings')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('restrict');
            
            // Unique constraint: satu paket hanya bisa muncul sekali dalam satu packing
            $table->unique(['packing_id', 'package_id']);
            
            $table->index('packing_id');
            $table->index('package_id');
            $table->index('is_placed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_packages');
    }
};