<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Estatísticas e dashboard"
 * )
 */
class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/stats",
     *     summary="Obter estatísticas do dashboard",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas do dashboard",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_tasks", type="integer", example=25),
     *             @OA\Property(property="completed_tasks", type="integer", example=15),
     *             @OA\Property(property="pending_tasks", type="integer", example=8),
     *             @OA\Property(property="in_progress_tasks", type="integer", example=2),
     *             @OA\Property(property="overdue_tasks", type="integer", example=3),
     *             @OA\Property(property="tasks_by_priority", type="object",
     *                 @OA\Property(property="alta", type="integer", example=10),
     *                 @OA\Property(property="media", type="integer", example=12),
     *                 @OA\Property(property="baixa", type="integer", example=3)
     *             ),
     *             @OA\Property(property="tasks_by_user", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="user_name", type="string", example="João Silva"),
     *                     @OA\Property(property="total", type="integer", example=5),
     *                     @OA\Property(property="completed", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin vê estatísticas de todas as empresas
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
            
            $tasksByUser = User::withCount([
                'tasks as total',
                'tasks as completed' => function ($query) {
                    $query->where('status', Task::STATUS_CONCLUIDA);
                }
            ])
            ->get()
            ->map(function ($user) {
                return [
                    'user_name' => $user->name,
                    'total' => $user->total,
                    'completed' => $user->completed,
                ];
            });
        } else {
            // User vê apenas estatísticas da sua empresa
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
            
            $tasksByUser = User::where('company_id', $companyId)
                ->withCount([
                    'tasks as total',
                    'tasks as completed' => function ($query) {
                        $query->where('status', Task::STATUS_CONCLUIDA);
                    }
                ])
                ->get()
                ->map(function ($user) {
                    return [
                        'user_name' => $user->name,
                        'total' => $user->total,
                        'completed' => $user->completed,
                    ];
                });
        }

        return response()->json([
            'stats' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'cancelled_tasks' => 0
            ],
            'status_distribution' => [
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'pending' => $pendingTasks
            ],
            'priority_distribution' => [
                'high' => $tasksByPriority['alta'],
                'medium' => $tasksByPriority['media'],
                'low' => $tasksByPriority['baixa']
            ],
            'top_users' => $tasksByUser->take(5)->map(function ($user) {
                return [
                    'user' => [
                        'name' => $user['user_name']
                    ],
                    'total_tasks' => $user['total'],
                    'completed_tasks' => $user['completed'],
                    'completion_rate' => $user['total'] > 0 ? round(($user['completed'] / $user['total']) * 100) : 0
                ];
            }),
            'overdue_tasks' => $overdueTasks,
            'tasks_by_priority' => $tasksByPriority,
            'tasks_by_user' => $tasksByUser,
        ]);
    }    /**
     * @OA\Get(
     *     path="/api/dashboard/recent-tasks",
     *     summary="Obter tarefas recentes",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Número de tarefas a retornar",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas recentes",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Task")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function recentTasks(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);
        
        $recentTasks = Task::forCompany($user->company_id)
            ->with(['user:id,name', 'company:id,name'])
            ->latest()
            ->limit($limit)
            ->get();
        
        return response()->json(['data' => $recentTasks]);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/my-tasks",
     *     summary="Obter tarefas do usuário logado",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Tarefas do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="pending", type="integer", example=3),
     *             @OA\Property(property="in_progress", type="integer", example=1),
     *             @OA\Property(property="completed", type="integer", example=5),
     *             @OA\Property(property="overdue", type="integer", example=1),
     *             @OA\Property(
     *                 property="tasks",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Task")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function myTasks(): JsonResponse
    {
        $user = Auth::user();
        
        $myTasks = Task::where('user_id', $user->id)
            ->with(['company:id,name'])
            ->get();
        
        $stats = [
            'pending' => $myTasks->where('status', Task::STATUS_PENDENTE)->count(),
            'in_progress' => $myTasks->where('status', Task::STATUS_EM_ANDAMENTO)->count(),
            'completed' => $myTasks->where('status', Task::STATUS_CONCLUIDA)->count(),
            'overdue' => $myTasks->filter(function ($task) {
                return $task->due_date && 
                       $task->due_date->lt(Carbon::today()) && 
                       in_array($task->status, [Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO]);
            })->count(),
        ];
        
        return response()->json([
            ...$stats,
            'tasks' => $myTasks
        ]);
    }
}
