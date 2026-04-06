<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'code' => 'SUB01',
                'city' => 'Surabaya',
                'address' => 'Jl. Raya Darmo No. 123, Surabaya, Jawa Timur',
                'phone' => '031-1234567',
                'is_active' => true,
            ],
            [
                'code' => 'JKT01',
                'city' => 'Jakarta',
                'address' => 'Jl. Sudirman No. 45, Jakarta Pusat, DKI Jakarta',
                'phone' => '021-7654321',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        $this->command->info('Branch seeded successfully!');
    }
}