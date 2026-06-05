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
        Schema::table('packings', function (Blueprint $table) {
            // Hapus kolom
            $table->dropColumn([
                'center_of_gravity_x',
                'center_of_gravity_y',
                'center_of_gravity_z',
                'algorithm_params',
                'raw_result'
            ]);
            
            // Tambah kolom baru
            $table->json('chromosome')->nullable()->after('fitness_score');
            $table->unsignedBigInteger('ga_parameter_id')->nullable()->after('chromosome');
            $table->integer('execution_time_ms')->nullable()->after('ga_parameter_id');
            
            // Foreign key ke ga_parameters
            $table->foreign('ga_parameter_id')->references('id')->on('ga_parameters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->dropForeign(['ga_parameter_id']);
            $table->dropColumn(['chromosome', 'ga_parameter_id', 'execution_time_ms']);
            $table->decimal('center_of_gravity_x', 8, 2)->nullable();
            $table->decimal('center_of_gravity_y', 8, 2)->nullable();
            $table->decimal('center_of_gravity_z', 8, 2)->nullable();
            $table->json('algorithm_params')->nullable();
            $table->json('raw_result')->nullable();
        });
    }
};
