<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * @OA\Tag(
 *     name="Export",
 *     description="Exportação de dados"
 * )
 */
class ExportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/export/tasks/csv",
     *     summary="Exportar tarefas para CSV",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pendente", "em_andamento", "concluida"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filtrar por prioridade",
     *         required=false,
     *         @OA\Schema(type="string", enum={"baixa", "media", "alta"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Data inicial",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Data final",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo CSV",
     *         @OA\MediaType(mediaType="text/csv")
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function tasksCsv(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode exportar todas as tarefas
            $query = Task::with(['user:id,name,email', 'company:id,name']);
        } else {
            // User pode exportar apenas tarefas da sua empresa
            $query = Task::forCompany($user->company_id)
                ->with(['user:id,name,email', 'company:id,name']);
        }
        
        // Aplicar filtros
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $tasks = $query->latest()->get();
        
        // Criar CSV
        $filename = 'tarefas_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Cabeçalhos
            fputcsv($file, [
                'ID',
                'Título',
                'Descrição',
                'Status',
                'Prioridade',
                'Data de Vencimento',
                'Responsável',
                'E-mail do Responsável',
                'Empresa',
                'Criado em',
                'Atualizado em'
            ]);
            
            // Dados
            foreach ($tasks as $task) {
                $statusMap = [
                    'pendente' => 'Pendente',
                    'em_andamento' => 'Em Andamento',
                    'concluida' => 'Concluída'
                ];
                
                $priorityMap = [
                    'baixa' => 'Baixa',
                    'media' => 'Média',
                    'alta' => 'Alta'
                ];
                
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->description ?: '',
                    $statusMap[$task->status] ?? $task->status,
                    $priorityMap[$task->priority] ?? $task->priority,
                    $task->due_date ? Carbon::parse($task->due_date)->format('d/m/Y') : '',
                    $task->user->name ?? 'N/A',
                    $task->user->email ?? 'N/A',
                    $task->company->name ?? 'N/A',
                    $task->created_at ? $task->created_at->format('d/m/Y H:i:s') : '',
                    $task->updated_at ? $task->updated_at->format('d/m/Y H:i:s') : ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * @OA\Get(
     *     path="/api/export/report/summary",
     *     summary="Exportar relatório resumido",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Relatório resumido em JSON",
     *         @OA\JsonContent(
     *             @OA\Property(property="company_name", type="string"),
     *             @OA\Property(property="generated_at", type="string"),
     *             @OA\Property(property="period", type="string"),
     *             @OA\Property(property="summary", type="object"),
     *             @OA\Property(property="tasks_by_status", type="object"),
     *             @OA\Property(property="tasks_by_priority", type="object"),
     *             @OA\Property(property="users_performance", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function reportSummary(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin vê dados de todas as empresas
            $totalTasks = Task::count();
            $completedTasks = Task::byStatus(Task::STATUS_CONCLUIDA)->count();
            $pendingTasks = Task::byStatus(Task::STATUS_PENDENTE)->count();
            $inProgressTasks = Task::byStatus(Task::STATUS_EM_ANDAMENTO)->count();
            
            $overdueTasks = Task::where('due_date', '<', Carbon::today())
                ->whereIn('status', [Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO])
                ->count();
            
            $tasksByPriority = [
                'alta' => Task::byPriority(Task::PRIORITY_ALTA)->count(),
                'media' => Task::byPriority(Task::PRIORITY_MEDIA)->count(),
                'baixa' => Task::byPriority(Task::PRIORITY_BAIXA)->count(),
            ];
            
            $usersPerformance = \App\Models\User::withCount([
                'tasks as total_tasks',
                'tasks as completed_tasks' => function ($query) {
                    $query->where('status', Task::STATUS_CONCLUIDA);
                },
                'tasks as overdue_tasks' => function ($query) {
                    $query->where('due_date', '<', Carbon::today())
                          ->whereIn('status', [Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO]);
                }
            ])
            ->get()
            ->map(function ($user) {
                $completionRate = $user->total_tasks > 0 ? 
                    round(($user->completed_tasks / $user->total_tasks) * 100, 2) : 0;
                
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_tasks' => $user->total_tasks,
                    'completed_tasks' => $user->completed_tasks,
                    'overdue_tasks' => $user->overdue_tasks,
                    'completion_rate' => $completionRate . '%'
                ];
            });
            
            $companyName = 'Todas as Empresas';
        } else {
            // User vê apenas dados da sua empresa
            $companyId = $user->company_id;
            
            $totalTasks = Task::forCompany($companyId)->count();
            $completedTasks = Task::forCompany($companyId)->byStatus(Task::STATUS_CONCLUIDA)->count();
            $pendingTasks = Task::forCompany($companyId)->byStatus(Task::STATUS_PENDENTE)->count();
            $inProgressTasks = Task::forCompany($companyId)->byStatus(Task::STATUS_EM_ANDAMENTO)->count();
            
            $overdueTasks = Task::forCompany($companyId)
                ->where('due_date', '<', Carbon::today())
                ->whereIn('status', [Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO])
                ->count();
            
            $tasksByPriority = [
                'alta' => Task::forCompany($companyId)->byPriority(Task::PRIORITY_ALTA)->count(),
                'media' => Task::forCompany($companyId)->byPriority(Task::PRIORITY_MEDIA)->count(),
                'baixa' => Task::forCompany($companyId)->byPriority(Task::PRIORITY_BAIXA)->count(),
            ];
            
            $usersPerformance = \App\Models\User::where('company_id', $companyId)
                ->withCount([
                    'tasks as total_tasks',
                    'tasks as completed_tasks' => function ($query) {
                        $query->where('status', Task::STATUS_CONCLUIDA);
                    },
                    'tasks as overdue_tasks' => function ($query) {
                        $query->where('due_date', '<', Carbon::today())
                              ->whereIn('status', [Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO]);
                    }
                ])
                ->get()
                ->map(function ($user) {
                    $completionRate = $user->total_tasks > 0 ? 
                        round(($user->completed_tasks / $user->total_tasks) * 100, 2) : 0;
                    
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'total_tasks' => $user->total_tasks,
                        'completed_tasks' => $user->completed_tasks,
                        'overdue_tasks' => $user->overdue_tasks,
                        'completion_rate' => $completionRate . '%'
                    ];
                });
            
            $companyName = $user->company->name;
        }
        
        return response()->json([
            'company_name' => $companyName,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'period' => 'Todos os períodos',
            'summary' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) . '%' : '0%'
            ],
            'tasks_by_status' => [
                'concluida' => $completedTasks,
                'em_andamento' => $inProgressTasks,
                'pendente' => $pendingTasks
            ],
            'tasks_by_priority' => $tasksByPriority,
            'users_performance' => $usersPerformance
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/export/tasks/pdf",
     *     summary="Exportar tarefas para PDF",
     *     tags={"Export"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pendente", "em_andamento", "concluida"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filtrar por prioridade",
     *         required=false,
     *         @OA\Schema(type="string", enum={"baixa", "media", "alta"})
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Data inicial",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Data final",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo PDF",
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function tasksPdf(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode exportar todas as tarefas
            $query = Task::with(['user:id,name,email', 'company:id,name']);
        } else {
            // User pode exportar apenas tarefas da sua empresa
            $query = Task::forCompany($user->company_id)
                ->with(['user:id,name,email', 'company:id,name']);
        }
        
        // Aplicar filtros
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $tasks = $query->latest()->get();
        
        // Gerar estatísticas
        $total = $tasks->count();
        $completed = $tasks->where('status', 'concluida')->count();
        $inProgress = $tasks->where('status', 'em_andamento')->count();
        $pending = $tasks->where('status', 'pendente')->count();
        
        $statusLabels = [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento', 
            'concluida' => 'Concluída'
        ];
        
        $priorityLabels = [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta'
        ];
        
        // Criar HTML do PDF
        $html = view('exports.tasks-pdf', [
            'tasks' => $tasks,
            'stats' => [
                'total' => $total,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'pending' => $pending
            ],
            'company' => $user->isAdmin() ? 'Todas as Empresas' : $user->company->name,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
            'statusLabels' => $statusLabels,
            'priorityLabels' => $priorityLabels,
            'filters' => $request->all()
        ])->render();
        
        // Usar DomPDF para gerar o PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'relatorio-tarefas-' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
