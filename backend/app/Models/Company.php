<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Company",
 *     required={"id", "name", "identifier"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="SmartLead"),
 *     @OA\Property(property="identifier", type="string", example="SMARTLEAD"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Empresa de tecnologia"),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'identifier',
        'description',
    ];

    /**
     * Relacionamento com usuários
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relacionamento com tarefas
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
