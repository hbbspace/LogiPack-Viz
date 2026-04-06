<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number', 50)->unique();
            $table->enum('status', ['scheduled', 'in_transit', 'completed', 'cancelled'])->default('scheduled');
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('vehicle_plate', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->unsignedBigInteger('packing_id');
            $table->unsignedBigInteger('branch_origin_id');
            $table->unsignedBigInteger('branch_destination_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign key constraints
            $table->foreign('packing_id')->references('id')->on('packings')->onDelete('restrict');
            $table->foreign('branch_origin_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('branch_destination_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('delivery_number');
            $table->index('status');
            $table->index('packing_id');
            $table->index('branch_origin_id');
            $table->index('branch_destination_id');
            $table->index('scheduled_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};