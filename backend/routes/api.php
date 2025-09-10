<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;

// Health check para Docker
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rotas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Rotas protegidas
Route::middleware(['auth:api', 'company.access'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('tasks', TaskController::class);
    
    // Empresas - acesso diferenciado por role
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('companies/{company}', [CompanyController::class, 'show']);
    
    // Apenas admins podem criar/editar/deletar empresas
    Route::middleware('admin')->group(function () {
        Route::post('companies', [CompanyController::class, 'store']);
        Route::put('companies/{company}', [CompanyController::class, 'update']);
        Route::delete('companies/{company}', [CompanyController::class, 'destroy']);
    });
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats']);
        Route::get('recent-tasks', [DashboardController::class, 'recentTasks']);
        Route::get('my-tasks', [DashboardController::class, 'myTasks']);
    });
    
    // Exportação
    Route::prefix('export')->group(function () {
        Route::get('tasks/csv', [ExportController::class, 'tasksCsv']);
        Route::get('report/summary', [ExportController::class, 'reportSummary']);
    });
    
    // Perfil do usuário
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('password', [ProfileController::class, 'changePassword']);
        Route::get('stats', [ProfileController::class, 'stats']);
        Route::delete('/', [ProfileController::class, 'destroy']);
    });
});