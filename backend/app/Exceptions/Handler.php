<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Se a requisição espera JSON (API)
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions
     */
    private function handleApiException($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'error' => 'Dados de entrada inválidos',
                'message' => 'Os dados fornecidos são inválidos',
                'errors' => $exception->errors()
            ], 422);
        }

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Recurso não encontrado',
                'message' => 'O recurso solicitado não foi encontrado'
            ], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Rota não encontrada',
                'message' => 'A rota solicitada não existe'
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error' => 'Método não permitido',
                'message' => 'O método HTTP usado não é permitido para esta rota'
            ], 405);
        }

        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'error' => 'Token expirado',
                'message' => 'Seu token de acesso expirou'
            ], 401);
        }

        if ($exception instanceof TokenInvalidException) {
            return response()->json([
                'error' => 'Token inválido',
                'message' => 'Seu token de acesso é inválido'
            ], 401);
        }

        if ($exception instanceof JWTException) {
            return response()->json([
                'error' => 'Token não encontrado',
                'message' => 'Token de acesso não foi fornecido'
            ], 401);
        }

        // Para outros erros, retorna erro genérico em produção
        if (config('app.debug')) {
            return response()->json([
                'error' => 'Erro interno do servidor',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ], 500);
        }

        return response()->json([
            'error' => 'Erro interno do servidor',
            'message' => 'Ocorreu um erro interno no servidor'
        ], 500);
    }
}
