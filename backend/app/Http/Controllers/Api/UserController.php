<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Listar usuários",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por nome ou email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filtrar por role",
     *         required=false,
     *         @OA\Schema(type="string", enum={"admin","user"})
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="Filtrar por empresa",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $perPage = $request->get('per_page', 15);
        
        $query = User::with('company:id,name');
        
        if ($user->isAdmin()) {
            // Admin vê todos os usuários de todas as empresas
            
            // Filtros para admin
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }
            
            if ($request->has('company_id') && $request->company_id) {
                $query->where('company_id', $request->company_id);
            }
        } else {
            // User vê apenas usuários da mesma empresa
            $query->where('company_id', $user->company_id);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Criar novo usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Maria Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="maria@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário criado com sucesso"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user', // Default para user
        ];
        
        if ($currentUser->isAdmin()) {
            // Admin pode criar usuário para qualquer empresa
            $userData['company_id'] = $request->company_id ?? $currentUser->company_id;
        } else {
            // User só pode criar para sua empresa
            $userData['company_id'] = $currentUser->company_id;
        }
        
        $user = User::create($userData);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user->load('company')
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Obter usuário específico",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function show(User $user): JsonResponse
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        
        // Admin pode ver qualquer usuário, user só da mesma empresa
        if (!$currentUser->isAdmin() && $user->company_id !== $currentUser->company_id) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        
        return response()->json(['user' => $user->load('company')]);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Atualizar usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Maria Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="maria@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        
        // Admin pode editar qualquer usuário, user só da mesma empresa
        if (!$currentUser->isAdmin() && $user->company_id !== $currentUser->company_id) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
            'role' => 'sometimes|in:admin,user',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        
        // Apenas admin pode alterar company_id
        if ($currentUser->isAdmin() && $request->has('company_id')) {
            $data['company_id'] = $request->company_id;
        }
        
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $user->fresh()
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Deletar usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário deletado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado"
     *     )
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        
        // Admin pode deletar qualquer usuário, user só da mesma empresa
        if (!$currentUser->isAdmin() && $user->company_id !== $currentUser->company_id) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        
        // Não permitir que o usuário delete a si mesmo
        if ($user->id === $currentUser->id) {
            return response()->json(['message' => 'Não é possível deletar seu próprio usuário'], 422);
        }
        
        $user->delete();

        return response()->json([
            'message' => 'Usuário deletado com sucesso'
        ]);
    }
}
