<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateFirstCompanyAndUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartlead:create-first {--company-name= : Nome da empresa} {--company-identifier= : Identificador da empresa} {--user-name= : Nome do usuário} {--user-email= : Email do usuário} {--user-password= : Senha do usuário}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar a primeira empresa e usuário do sistema SmartLead';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Criando primeira empresa e usuário do SmartLead...');

        // Perguntar pelos dados se não foram fornecidos
        $companyName = $this->option('company-name') ?: $this->ask('Nome da empresa');
        $companyIdentifier = $this->option('company-identifier') ?: $this->ask('Identificador da empresa (ex: SMARTLEAD)');
        $userName = $this->option('user-name') ?: $this->ask('Nome do usuário');
        $userEmail = $this->option('user-email') ?: $this->ask('Email do usuário');
        $userPassword = $this->option('user-password') ?: $this->secret('Senha do usuário');

        // Criar empresa
        $company = Company::create([
            'name' => $companyName,
            'identifier' => strtoupper($companyIdentifier),
            'description' => 'Empresa criada via comando artisan'
        ]);

        $this->info("Empresa '{$company->name}' criada com sucesso!");

        // Criar usuário
        $user = User::create([
            'name' => $userName,
            'email' => $userEmail,
            'password' => Hash::make($userPassword),
            'company_id' => $company->id
        ]);

        $this->info("Usuário '{$user->name}' criado com sucesso!");
        $this->info("Email: {$user->email}");
        $this->info("Empresa: {$company->name}");

        $this->newLine();
        $this->info('Sistema SmartLead configurado com sucesso!');
        $this->info('Você pode fazer login com as credenciais acima.');

        return 0;
    }
}
