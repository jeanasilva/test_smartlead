<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar usuário admin
        User::updateOrCreate(
            ['email' => 'admin@smartlead.com'],
            [
                'name' => 'Admin SmartLead',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'company_id' => 1, // SmartLead
            ]
        );

        // Criar usuário de teste
        User::updateOrCreate(
            ['email' => 'joao@exemplo.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'company_id' => 1, // SmartLead
            ]
        );

        // Criar usuário de teste 2 (empresa diferente)
        User::updateOrCreate(
            ['email' => 'maria@exemplo.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'company_id' => 2, // Tech Solutions
            ]
        );

        // Criar usuário de teste 3 (empresa diferente)
        User::updateOrCreate(
            ['email' => 'carlos@mktdigital.com'],
            [
                'name' => 'Carlos Lima',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'company_id' => 3, // Marketing Digital
            ]
        );
    }
}
