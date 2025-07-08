<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Tableros Kanban",
 *     description="API para la gestión de tableros, columnas y tareas tipo Kanban. Permite a los usuarios crear tableros personalizados, definir columnas (estados) y gestionar tareas de manera dinámica. Todas las rutas están protegidas por autenticación y auditoría.",
 *     @OA\Contact(
 *         email="soporte@tusitio.com",
 *         name="Soporte API"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API principal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
