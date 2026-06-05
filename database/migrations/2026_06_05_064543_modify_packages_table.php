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
        Schema::table('packages', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn([
                'shipper',
                'shipper_address',
                'recipient',
                'recipient_address',
                'delivered_at'
            ]);
            
            // Tambah kolom batch_import_id
            $table->unsignedBigInteger('batch_import_id')->nullable()->after('id');
            
            // Foreign key ke batch_imports
            $table->foreign('batch_import_id')->references('id')->on('batch_imports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['batch_import_id']);
            $table->dropColumn('batch_import_id');
            $table->string('shipper', 200);
            $table->text('shipper_address');
            $table->string('recipient', 200);
            $table->text('recipient_address');
            $table->timestamp('delivered_at')->nullable();
        });
    }
};
