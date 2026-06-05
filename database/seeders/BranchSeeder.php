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
                'code' => 'MLG01',
                'city' => 'Malang',
                'address' => 'Jl. Merdeka Selatan No.5, Kauman, Kec. Klojen, Kota Malang, Jawa Timur 65119',
                'phone' => '62 859-5247-6164',
                'is_active' => true,
            ],
            [
                'code' => 'MLG02',
                'city' => 'Malang',
                'address' => 'Kasin, Kec. Klojen, Kota Malang, Jawa Timur 65117',
                'phone' => '034-1362255',
                'is_active' => true,
            ],
            [
                'code' => 'SUB01',
                'city' => 'Surabaya',
                'address' => 'Jl. Kebon Rojo No.10, Krembangan Sel., Kec. Krembangan, Surabaya, Jawa Timur 60175',
                'phone' => '031-3522096',
                'is_active' => false,
            ],
            [
                'code' => 'JKT01',
                'city' => 'Jakarta',
                'address' => 'Jl. Lap. Banteng Utara No.1, Ps. Baru, Kecamatan Sawah Besar, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10710',
                'phone' => '021-3520037',
                'is_active' => false,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        $this->command->info('Branch seeded successfully!');
    }
}