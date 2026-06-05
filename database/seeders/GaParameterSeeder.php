<?php

namespace Database\Seeders;

use App\Models\GaParameter;
use Illuminate\Database\Seeder;

class GaParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ga_parameters = [
            [
            'name' => 'GA Optimal Parameters',
            'population_size' => 100,
            'generation_limit' => 150,
            'crossover_rate' => 0.1,
            'mutation_rate' => 0.9,
            'is_active' => true,
            'created_by' => 1,
            'updated_by' => 1,
            ],
        ];

        foreach ($ga_parameters as $parameter) {
            GaParameter::create($parameter);
        }

        $this->command->info('GA Parameters seeded successfully!');
    }
}