<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Contoh: "Mobil Box A", "Van Besar"
            $table->string('type_code', 50)->nullable(); // Contoh: "BOX-A", "VAN-L"
            $table->integer('length')->comment('Panjang dalam cm');
            $table->integer('width')->comment('Lebar dalam cm');
            $table->integer('height')->comment('Tinggi dalam cm');
            $table->integer('volume_max')->comment('Volume maksimal dalam cm³ (bisa auto-calculate)');
            $table->decimal('weight_max', 10, 2)->comment('Berat maksimal dalam kg');
            $table->string('image_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('type_code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};