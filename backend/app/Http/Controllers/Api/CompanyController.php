<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Companies",
 *     description="Operações relacionadas às empresas"
 * )
 */
class CompanyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/companies",
     *     summary="Listar todas as empresas",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Company"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function index(): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user->isAdmin()) {
            // Admin pode ver todas as empresas
            $companies = Company::with('users')->get();
        } else {
            // Usuário só pode ver sua própria empresa
            $companies = Company::with(['users' => function($query) use ($user) {
                $query->where('company_id', $user->company_id);
            }])->where('id', $user->company_id)->get();
        }
        
        return response()->json(['data' => $companies]);
    }

    /**
     * @OA\Post(
     *     path="/api/companies",
     *     summary="Criar nova empresa",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "identifier"},
     *             @OA\Property(property="name", type="string", example="Empresa ABC"),
     *             @OA\Property(property="identifier", type="string", example="ABC001"),
     *             @OA\Property(property="description", type="string", example="Empresa de tecnologia")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empresa criada com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:50|unique:companies,identifier',
            'description' => 'nullable|string',
        ]);
        
        $company = Company::create($validated);
        
        return response()->json([
            'message' => 'Empresa criada com sucesso',
            'data' => $company
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{id}",
     *     summary="Exibir uma empresa específica",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID da empresa", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da empresa",
     *         @OA\JsonContent(@OA\Property(property="data", ref="#/components/schemas/Company"))
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $company = Company::with(['users', 'tasks'])->findOrFail($id);
        return response()->json(['data' => $company]);
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{id}",
     *     summary="Atualizar uma empresa",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID da empresa", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Empresa ABC Atualizada"),
     *             @OA\Property(property="identifier", type="string", example="ABC002"),
     *             @OA\Property(property="description", type="string", example="Descrição atualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa atualizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empresa atualizada com sucesso"),
     *             @OA\Property(property="data", ref="#/components/schemas/Company")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro de validação"),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $company = Company::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'identifier' => 'sometimes|string|max:50|unique:companies,identifier,' . $id,
            'description' => 'nullable|string',
        ]);
        
        $company->update($validated);
        
        return response()->json([
            'message' => 'Empresa atualizada com sucesso',
            'data' => $company
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{id}",
     *     summary="Excluir uma empresa",
     *     tags={"Companies"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", description="ID da empresa", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa excluída com sucesso",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Empresa excluída com sucesso"))
     *     ),
     *     @OA\Response(response=404, description="Empresa não encontrada"),
     *     @OA\Response(response=401, description="Não autorizado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $company = Company::findOrFail($id);
        $company->delete();
        
        return response()->json([
            'message' => 'Empresa excluída com sucesso'
        ]);
    }
}
