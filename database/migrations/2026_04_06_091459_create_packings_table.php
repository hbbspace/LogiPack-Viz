<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable()->comment('Nama/deskripsi packing');
            $table->decimal('volume_utilization', 5, 2)->comment('Persentase utilisasi volume (0-100)');
            $table->decimal('weight_utilization', 5, 2)->comment('Persentase utilisasi berat (0-100)');
            $table->decimal('fitness_score', 10, 2)->nullable()->comment('Skor fitness dari GA');
            $table->decimal('center_of_gravity_x', 8, 2)->nullable();
            $table->decimal('center_of_gravity_y', 8, 2)->nullable();
            $table->decimal('center_of_gravity_z', 8, 2)->nullable();
            $table->string('visualization_file_path')->nullable()->comment('Path ke file HTML visualisasi');
            $table->json('algorithm_params')->nullable()->comment('Parameter GA yang digunakan');
            $table->json('raw_result')->nullable()->comment('Hasil lengkap dari GA (JSON)');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->unsignedBigInteger('container_id');
            $table->unsignedBigInteger('branch_id')->comment('Cabang tempat packing dilakukan');
            $table->unsignedBigInteger('user_id')->comment('User yang melakukan packing');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign key constraints
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('restrict');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('container_id');
            $table->index('branch_id');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('fitness_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packings');
    }
};