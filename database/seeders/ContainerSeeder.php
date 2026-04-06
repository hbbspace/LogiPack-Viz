<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Container;

class ContainerSeeder extends Seeder
{
    public function run(): void
    {
        // Container sesuai dengan spek di notebook
        $containers = [
            [
                'name' => 'Mobil Box Standar',
                'type_code' => 'BOX-STD',
                'length' => 6,
                'width' => 5,
                'height' => 5,
                'weight_max' => 50,
                'description' => 'Container standar untuk pengiriman lokal (6x5x5 cm dalam skala model)',
                'is_active' => true,
            ],
            [
                'name' => 'Mobil Box Besar',
                'type_code' => 'BOX-LRG',
                'length' => 10,
                'width' => 8,
                'height' => 8,
                'weight_max' => 100,
                'description' => 'Container besar untuk pengiriman volume tinggi (10x8x8 cm dalam skala model)',
                'is_active' => true,
            ],
            [
                'name' => 'Van Kecil',
                'type_code' => 'VAN-SML',
                'length' => 5,
                'width' => 4,
                'height' => 4,
                'weight_max' => 30,
                'description' => 'Van kecil untuk pengiriman ekspres (5x4x4 cm dalam skala model)',
                'is_active' => true,
            ],
        ];

        foreach ($containers as $container) {
            Container::create($container);
        }

        $this->command->info('Container seeded successfully!');
    }
}