<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     * Garante que o usuário só acesse dados da própria empresa
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        // Verificar se o usuário está autenticado
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }
        
        // Verificar se o usuário tem uma empresa associada
        if (!$user->company_id) {
            return response()->json(['message' => 'Usuário não possui empresa associada'], 403);
        }
        
        // Adicionar company_id ao request para facilitar uso nos controllers
        $request->merge(['user_company_id' => $user->company_id]);
        
        return $next($request);
    }
}
