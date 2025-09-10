<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar empresa padrão
        Company::create([
            'name' => 'SmartLead',
            'identifier' => 'SMARTLEAD',
            'description' => 'Empresa padrão para usuários existentes'
        ]);
        
        // Criar mais algumas empresas de exemplo
        Company::create([
            'name' => 'Tech Solutions',
            'identifier' => 'TECHSOL',
            'description' => 'Soluções em tecnologia'
        ]);
        
        Company::create([
            'name' => 'Marketing Digital',
            'identifier' => 'MKTDIGITAL',
            'description' => 'Agência de marketing digital'
        ]);
    }
}
