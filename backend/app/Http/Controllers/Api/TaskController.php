<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TaskCreated;
use App\Notifications\TaskCompleted;
use App\Notifications\TaskUpdated;
use App\Notifications\TaskAssigned;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="Operações relacionadas às tarefas"
 * )
 */
class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Listar todas as tarefas da empresa",
     *     tags={"Tasks"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas",
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
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin vê todas as tarefas de todas as empresas
            $query = Task::with(['user:id,name,email', 'company:id,name']);
        } else {
            // User vê apenas tarefas da sua empresa
            $query = Task::forCompany($user->company_id)
                ->with(['user:id,name,email', 'company:id,name']);
        }
        
        // Filtros opcionais
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }
        
        $tasks = $query->latest()->get();
        
        return response()->json(['data' => $tasks]);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Criar nova tarefa",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "status", "priority"},
     *             @OA\Property(property="title", type="string", example="Finalizar relatório"),
     *             @OA\Property(property="description", type="string", example="Finalizar o relatório mensal de vendas"),
     *             @OA\Property(property="status", type="string", enum={"pendente", "em_andamento", "concluida"}, example="pendente"),
     *             @OA\Property(property="priority", type="string", enum={"baixa", "media", "alta"}, example="media"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="user_id", type="integer", example=1, description="ID do usuário responsável (opcional, padrão: usuário logado)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarefa criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarefa criada com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in([Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO, Task::STATUS_CONCLUIDA])],
            'priority' => ['required', Rule::in([Task::PRIORITY_BAIXA, Task::PRIORITY_MEDIA, Task::PRIORITY_ALTA])],
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        // Verificar se o user_id pertence à mesma empresa
        if (isset($validated['user_id'])) {
            $assignedUser = \App\Models\User::find($validated['user_id']);
            if ($assignedUser->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Usuário não pertence à sua empresa'
                ], 403);
            }
        }
        
        $validated['company_id'] = $user->company_id;
        $validated['user_id'] = $validated['user_id'] ?? $user->id;
        
        try {
            $task = Task::create($validated);
            $task->load(['user:id,name,email', 'company:id,name']);
            
            // Enviar notificação adequada baseada no contexto
            if ($validated['user_id'] === $user->id) {
                // Usuário criou tarefa para si mesmo
                $task->user->notify(new TaskCreated($task));
            } else {
                // Tarefa foi atribuída a outro usuário
                $task->user->notify(new TaskAssigned($task, $user));
            }
            
            return response()->json([
                'message' => 'Tarefa criada com sucesso',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar tarefa: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao criar tarefa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     summary="Exibir uma tarefa específica",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da tarefa",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode ver qualquer tarefa
            $task = Task::with(['user:id,name,email', 'company:id,name'])
                ->findOrFail($id);
        } else {
            // User vê apenas tarefas da sua empresa
            $task = Task::forCompany($user->company_id)
                ->with(['user:id,name,email', 'company:id,name'])
                ->findOrFail($id);
        }
        
        return response()->json(['data' => $task]);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Atualizar uma tarefa",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Finalizar relatório"),
     *             @OA\Property(property="description", type="string", example="Finalizar o relatório mensal de vendas"),
     *             @OA\Property(property="status", type="string", enum={"pendente", "em_andamento", "concluida"}, example="em_andamento"),
     *             @OA\Property(property="priority", type="string", enum={"baixa", "media", "alta"}, example="alta"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2025-12-31"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarefa atualizada com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Task")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=404, description="Tarefa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode editar qualquer tarefa
            $task = Task::findOrFail($id);
        } else {
            // User pode editar apenas tarefas da sua empresa
            $task = Task::forCompany($user->company_id)->findOrFail($id);
        }
        
        // Guardar valores originais para detectar mudanças
        $originalData = $task->toArray();
        $originalUserName = $task->user->name;
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => ['sometimes', Rule::in([Task::STATUS_PENDENTE, Task::STATUS_EM_ANDAMENTO, Task::STATUS_CONCLUIDA])],
            'priority' => ['sometimes', Rule::in([Task::PRIORITY_BAIXA, Task::PRIORITY_MEDIA, Task::PRIORITY_ALTA])],
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_id' => 'nullable|exists:users,id',
        ]);
        
        // Verificar se o user_id pertence à mesma empresa (apenas para users não admin)
        if (isset($validated['user_id']) && !$user->isAdmin()) {
            $assignedUser = \App\Models\User::find($validated['user_id']);
            if ($assignedUser->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'Usuário não pertence à sua empresa'
                ], 403);
            }
        }
        
        // Verificar se a tarefa foi marcada como concluída
        $wasCompleted = $task->status === Task::STATUS_CONCLUIDA;
        $nowCompleted = isset($validated['status']) && $validated['status'] === Task::STATUS_CONCLUIDA;
        
        // Verificar se houve mudança de responsável
        $userChanged = isset($validated['user_id']) && $validated['user_id'] !== $task->user_id;
        $oldUserId = $task->user_id;
        
        try {
            $task->update($validated);
            $task->load(['user:id,name,email', 'company:id,name']);
            
            // Detectar mudanças para notificação
            $changes = [];
            foreach (['title', 'description', 'status', 'priority', 'due_date'] as $field) {
                if (isset($validated[$field]) && $originalData[$field] != $validated[$field]) {
                    $changes[$field] = [
                        'from' => $this->formatFieldValue($field, $originalData[$field]),
                        'to' => $this->formatFieldValue($field, $validated[$field])
                    ];
                }
            }
            
            // Notificações baseadas nas mudanças
            if ($nowCompleted && !$wasCompleted) {
                // Tarefa foi concluída
                $task->user->notify(new TaskCompleted($task));
            } elseif ($userChanged) {
                // Tarefa foi reatribuída
                $oldUser = \App\Models\User::find($oldUserId);
                if ($oldUser) {
                    $changes['user_id'] = [
                        'from' => $originalUserName,
                        'to' => $task->user->name
                    ];
                    $oldUser->notify(new TaskUpdated($task, $changes));
                }
                $task->user->notify(new TaskAssigned($task, $user));
            } elseif (!empty($changes)) {
                // Outras atualizações
                $task->user->notify(new TaskUpdated($task, $changes));
            }
            
            return response()->json([
                'message' => 'Tarefa atualizada com sucesso',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar tarefa: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erro ao atualizar tarefa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Excluir uma tarefa",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da tarefa",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarefa excluída com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tarefa excluída com sucesso")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tarefa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode deletar qualquer tarefa
            $task = Task::findOrFail($id);
        } else {
            // User pode deletar apenas tarefas da sua empresa
            $task = Task::forCompany($user->company_id)->findOrFail($id);
        }
        
        $task->delete();
        
        return response()->json([
            'message' => 'Tarefa excluída com sucesso'
        ]);
    }
    
    /**
     * Formatar valor do campo para exibição
     */
    private function formatFieldValue($field, $value)
    {
        switch ($field) {
            case 'status':
                $labels = [
                    'pendente' => 'Pendente',
                    'em_andamento' => 'Em Andamento',
                    'concluida' => 'Concluída'
                ];
                return $labels[$value] ?? $value;
            case 'priority':
                $labels = [
                    'baixa' => 'Baixa',
                    'media' => 'Média',
                    'alta' => 'Alta'
                ];
                return $labels[$value] ?? $value;
            case 'due_date':
                return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : 'Não definido';
            default:
                return $value ?: 'Não definido';
        }
    }
}
