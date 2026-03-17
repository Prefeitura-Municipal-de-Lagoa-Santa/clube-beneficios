<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrador',
                'email' => 'admin@lagoasanta.mg.gov.br',
                'password' => bcrypt('admin123'),
                'matricula' => '000001',
            ]
        );

        $admin->assignRole('admin');
    }
}
