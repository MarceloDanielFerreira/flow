<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAuthMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  Roles permitidos para acceder
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verifica si el usuario está autenticado usando el guard 'sanctum'
        $user = \Illuminate\Support\Facades\Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Si no se especifican roles, solo requiere autenticación
        if (empty($roles)) {
            return $next($request);
        }

        // Verifica si el usuario tiene uno de los roles permitidos
        if (!in_array($user->getRole(), $roles)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return $next($request);
    }
} 