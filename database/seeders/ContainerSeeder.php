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
                'name' => 'Daihatsu Gran Max Van',
                'type_code' => 'VAN-SML',
                'length' => 215,
                'width' => 150,
                'height' => 135,
                'weight_max' => 720,
                'description' => 'Mobil Van kecil untuk pengiriman ekspres (215x150x135 cm dalam skala model)',
                'is_active' => true,
            ],
            [
                'name' => 'Daihatsu Gran Max Box',
                'type_code' => 'BOX-SML',
                'length' => 240,
                'width' => 160,
                'height' => 130,
                'weight_max' => 800,
                'description' => 'Mobil Box kecil untuk pengiriman dalam kota (240x160x130 cm dalam skala model)',
                'is_active' => true,
            ],
            [
                'name' => 'Mobil Box Standar',
                'type_code' => 'BOX-TEST',
                'length' => 6,
                'width' => 5,
                'height' => 5,
                'weight_max' => 50,
                'description' => 'Container standar untuk testing (6x5x5 cm dalam skala model)',
                'is_active' => false,
            ],
        ];

        foreach ($containers as $container) {
            Container::create($container);
        }

        $this->command->info('Container seeded successfully!');
    }
}