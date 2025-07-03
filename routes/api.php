<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\RoleAuthMiddleware;
use Illuminate\Support\Facades\Auth as AuthFacade;

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
