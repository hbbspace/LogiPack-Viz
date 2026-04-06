<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sub01 = Branch::where('code', 'SUB01')->first();
        $jkt01 = Branch::where('code', 'JKT01')->first();

        // Admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'name' => 'Administrator',
            'email' => 'admin@logistic.com',
            'phone' => '081234567890',
            'level' => 'admin',
            'is_active' => true,
            'branch_id' => null, // Admin tidak terikat branch
            'last_login_at' => null,
            'last_activity_at' => null,
        ]);

        // User di branch SUB01
        User::create([
            'username' => 'user_sub01',
            'password' => Hash::make('user123'),
            'name' => 'Petugas Surabaya',
            'email' => 'sub01@logistic.com',
            'phone' => '081234567891',
            'level' => 'user',
            'is_active' => true,
            'branch_id' => $sub01->id,
            'last_login_at' => null,
            'last_activity_at' => null,
        ]);

        // User di branch JKT01
        User::create([
            'username' => 'user_jkt01',
            'password' => Hash::make('user123'),
            'name' => 'Petugas Jakarta',
            'email' => 'jkt01@logistic.com',
            'phone' => '081234567892',
            'level' => 'user',
            'is_active' => true,
            'branch_id' => $jkt01->id,
            'last_login_at' => null,
            'last_activity_at' => null,
        ]);

        $this->command->info('User seeded successfully!');
    }
}