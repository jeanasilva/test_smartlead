<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Task",
 *     required={"id", "title", "status", "priority", "company_id", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Finalizar relatório"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Finalizar o relatório mensal de vendas"),
 *     @OA\Property(property="status", type="string", enum={"pendente", "em_andamento", "concluida"}, example="pendente"),
 *     @OA\Property(property="priority", type="string", enum={"baixa", "media", "alta"}, example="media"),
 *     @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2025-12-31"),
 *     @OA\Property(property="company_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'company_id',
        'user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * Status disponíveis para as tarefas
     */
    const STATUS_PENDENTE = 'pendente';
    const STATUS_EM_ANDAMENTO = 'em_andamento';
    const STATUS_CONCLUIDA = 'concluida';

    /**
     * Prioridades disponíveis para as tarefas
     */
    const PRIORITY_BAIXA = 'baixa';
    const PRIORITY_MEDIA = 'media';
    const PRIORITY_ALTA = 'alta';

    /**
     * Relacionamento com empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar tarefas por empresa
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope para filtrar tarefas por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar tarefas por prioridade
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
