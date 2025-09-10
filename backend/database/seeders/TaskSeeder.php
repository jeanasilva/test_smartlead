<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Company;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        $users = User::all();
        
        // Criar tarefas de exemplo para cada empresa
        foreach ($companies as $company) {
            $companyUsers = $users->where('company_id', $company->id);
            
            if ($companyUsers->isNotEmpty()) {
                // Tarefas para SmartLead
                if ($company->identifier === 'SMARTLEAD') {
                    Task::create([
                        'title' => 'Implementar autenticação JWT',
                        'description' => 'Implementar sistema de autenticação usando JWT para a API',
                        'status' => Task::STATUS_CONCLUIDA,
                        'priority' => Task::PRIORITY_ALTA,
                        'due_date' => now()->addDays(5),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                    
                    Task::create([
                        'title' => 'Criar CRUD de usuários',
                        'description' => 'Desenvolver endpoints para gerenciar usuários',
                        'status' => Task::STATUS_CONCLUIDA,
                        'priority' => Task::PRIORITY_MEDIA,
                        'due_date' => now()->addDays(3),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                    
                    Task::create([
                        'title' => 'Implementar sistema de tarefas',
                        'description' => 'Criar funcionalidades de criação, edição e listagem de tarefas',
                        'status' => Task::STATUS_EM_ANDAMENTO,
                        'priority' => Task::PRIORITY_ALTA,
                        'due_date' => now()->addDays(7),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                    
                    Task::create([
                        'title' => 'Documentação da API',
                        'description' => 'Criar documentação completa da API com Swagger',
                        'status' => Task::STATUS_PENDENTE,
                        'priority' => Task::PRIORITY_MEDIA,
                        'due_date' => now()->addDays(10),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                    
                    Task::create([
                        'title' => 'Frontend Vue.js',
                        'description' => 'Desenvolver interface frontend usando Vue.js 2',
                        'status' => Task::STATUS_PENDENTE,
                        'priority' => Task::PRIORITY_ALTA,
                        'due_date' => now()->addDays(14),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                } else {
                    // Tarefas genéricas para outras empresas
                    Task::create([
                        'title' => 'Reunião de planejamento',
                        'description' => 'Reunião semanal para alinhamento da equipe',
                        'status' => Task::STATUS_PENDENTE,
                        'priority' => Task::PRIORITY_MEDIA,
                        'due_date' => now()->addDays(2),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                    
                    Task::create([
                        'title' => 'Relatório mensal',
                        'description' => 'Preparar relatório de atividades do mês',
                        'status' => Task::STATUS_PENDENTE,
                        'priority' => Task::PRIORITY_BAIXA,
                        'due_date' => now()->addDays(30),
                        'company_id' => $company->id,
                        'user_id' => $companyUsers->first()->id,
                    ]);
                }
            }
        }
    }
}
