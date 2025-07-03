<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Audit;

class AuditMiddleware
{
    /**
     * Maneja una solicitud entrante y registra la auditoría.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // No auditar endpoints de autenticación
        if (str_starts_with($request->path(), 'api/auth')) {
            return $next($request);
        }

        $user = Auth::guard('sanctum')->user();
        $oldValues = null;
        $newValues = null;
        $action = $this->resolveAction($request->method());

        // Solo para métodos que modifican datos
        if (in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            // Intenta obtener el modelo antes del cambio si es posible
            $oldValues = $request->route('id') ? $this->getOldModelData($request) : null;
        }

        $response = $next($request);

        // Para creaciones y actualizaciones, captura los datos enviados
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $newValues = $request->all();
        }

        // Para eliminaciones, intenta capturar el modelo eliminado
        if ($request->method() === 'DELETE') {
            $newValues = null;
        }

        Audit::create([
            'user_id'     => $user?->id,
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'action'      => $action,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return $response;
    }

    private function resolveAction($method)
    {
        return match ($method) {
            'POST'   => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default  => 'read',
        };
    }

    private function getOldModelData(Request $request)
    {
        // Intenta obtener el modelo usando el parámetro 'id' de la ruta
        // Esto puede personalizarse según tus rutas/modelos
        $id = $request->route('id');
        if ($id) {
            // Aquí podrías intentar deducir el modelo según la ruta
            // Ejemplo: /api/users/{id} => App\Models\User
            // Este método puede mejorarse según tus necesidades
            return null;
        }
        return null;
    }
} 