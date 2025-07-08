<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\RoleAuthMiddleware;
use Illuminate\Support\Facades\Auth as AuthFacade;
use App\Http\Controllers\Api\BoardController;
use App\Http\Controllers\Api\ColumnController;
use App\Http\Controllers\Api\TaskController;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware([RoleAuthMiddleware::class . ':admin,user'])->post('/logout', function (\Illuminate\Http\Request $request) {
        return AuthFacade::guard('sanctum')->user()->currentAccessToken()->delete()
            ? response()->json(['message' => 'Sesión cerrada'])
            : response()->json(['message' => 'Error al cerrar sesión'], 500);
    });

    Route::middleware([RoleAuthMiddleware::class . ':admin'])->get('/me', function (\Illuminate\Http\Request $request) {
        return AuthFacade::guard('sanctum')->user();
    });
});

// Rutas para usuarios
Route::middleware([RoleAuthMiddleware::class . ':admin,user'])->group(function () {
    // Listar usuarios (admin y user)
    Route::get('/users', [UserController::class, 'index']);
    // Mostrar usuario (admin y user)
    Route::get('/users/{id}', [UserController::class, 'show']);
});

// Rutas restringidas solo para admin
Route::middleware([RoleAuthMiddleware::class . ':admin'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);     // Crear usuario
    Route::put('/users/{id}', [UserController::class, 'update']); // Actualizar usuario
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Eliminar usuario
});

Route::middleware(['auth:sanctum', 'auditoria'])->group(function () {
    // Rutas de tableros
    Route::apiResource('boards', BoardController::class);

    // Rutas de columnas dentro de un tablero
    Route::apiResource('boards.columns', ColumnController::class)->shallow();

    // Rutas de tareas dentro de un tablero
    Route::apiResource('boards.tasks', TaskController::class)->shallow();
});
