<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Branch;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $sub01 = Branch::where('code', 'SUB01')->first();
        $jkt01 = Branch::where('code', 'JKT01')->first();

        // 6 Paket sesuai dengan data di notebook
        $packages = [
            [
                'tracking_number' => 'PKT-001',
                'shipper' => 'PT Maju Jaya',
                'shipper_address' => 'Jl. Raya Darmo No. 123, Surabaya',
                'recipient' => 'CV Berkah Abadi',
                'recipient_address' => 'Jl. Sudirman No. 45, Jakarta',
                'length' => 4,
                'width' => 3,
                'height' => 2,
                'weight' => 8,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
            [
                'tracking_number' => 'PKT-002',
                'shipper' => 'UD Sumber Rezeki',
                'shipper_address' => 'Jl. Raya Darmo No. 124, Surabaya',
                'recipient' => 'PT Cahaya Abadi',
                'recipient_address' => 'Jl. Gatot Subroto No. 12, Jakarta',
                'length' => 5,
                'width' => 2,
                'height' => 2,
                'weight' => 12,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
            [
                'tracking_number' => 'PKT-003',
                'shipper' => 'CV Karya Mandiri',
                'shipper_address' => 'Jl. Raya Darmo No. 125, Surabaya',
                'recipient' => 'UD Makmur Sentosa',
                'recipient_address' => 'Jl. Thamrin No. 78, Jakarta',
                'length' => 3,
                'width' => 3,
                'height' => 3,
                'weight' => 10,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
            [
                'tracking_number' => 'PKT-004',
                'shipper' => 'PT Nusantara Jaya',
                'shipper_address' => 'Jl. Raya Darmo No. 126, Surabaya',
                'recipient' => 'CV Sumber Makmur',
                'recipient_address' => 'Jl. Kuningan No. 56, Jakarta',
                'length' => 2,
                'width' => 4,
                'height' => 2,
                'weight' => 5,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
            [
                'tracking_number' => 'PKT-005',
                'shipper' => 'UD Sejahtera',
                'shipper_address' => 'Jl. Raya Darmo No. 127, Surabaya',
                'recipient' => 'PT Bina Usaha',
                'recipient_address' => 'Jl. Rasuna Said No. 34, Jakarta',
                'length' => 4,
                'width' => 2,
                'height' => 1,
                'weight' => 3,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
            [
                'tracking_number' => 'PKT-006',
                'shipper' => 'CV Mutiara Abadi',
                'shipper_address' => 'Jl. Raya Darmo No. 128, Surabaya',
                'recipient' => 'UD Berkah Bersama',
                'recipient_address' => 'Jl. M.H. Thamrin No. 90, Jakarta',
                'length' => 2,
                'width' => 2,
                'height' => 4,
                'weight' => 7,
                'branch_origin_id' => $sub01->id,
                'branch_destination_id' => $jkt01->id,
                'status' => 'pending',
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        $this->command->info('Package seeded successfully!');
        $this->command->info('Total packages: ' . Package::count());
    }
}