<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 50)->unique();
            $table->string('shipper', 200);
            $table->text('shipper_address');
            $table->string('recipient', 200);
            $table->text('recipient_address');
            $table->integer('length')->comment('Panjang dalam cm');
            $table->integer('width')->comment('Lebar dalam cm');
            $table->integer('height')->comment('Tinggi dalam cm');
            $table->integer('volume')->comment('Volume dalam cm³ (auto-calculate)');
            $table->decimal('weight', 10, 2)->comment('Berat dalam kg');
            $table->enum('status', ['pending', 'packed', 'delivered'])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->unsignedBigInteger('branch_origin_id')->comment('Cabang asal paket');
            $table->unsignedBigInteger('branch_destination_id')->nullable()->comment('Cabang tujuan');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign key constraints
            $table->foreign('branch_origin_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('branch_destination_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('tracking_number');
            $table->index('status');
            $table->index('branch_origin_id');
            $table->index('branch_destination_id');
            $table->index(['status', 'branch_origin_id']); // Composite untuk query paket pending di cabang tertentu
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};