<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/boards",
     *     summary="Listar tableros del usuario autenticado",
     *     tags={"Boards"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tableros",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales"),
     *                     @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tableros obtenidos correctamente.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $boards = $request->user()->boards()->with('columns', 'tasks')->get();
        return response()->json([
            'data' => $boards,
            'mensaje' => 'Tableros obtenidos correctamente.'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/boards",
     *     summary="Crear un nuevo tablero",
     *     tags={"Boards"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tablero creado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tablero creado exitosamente.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $board = $request->user()->boards()->create($data);
        return response()->json([
            'data' => $board,
            'mensaje' => 'Tablero creado exitosamente.'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/boards/{id}",
     *     summary="Mostrar un tablero específico",
     *     tags={"Boards"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos del tablero",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tablero obtenido correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No autorizado para ver este tablero.")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        $board = Board::with('columns', 'tasks')->findOrFail($id);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para ver este tablero.'
            ], 403);
        }
        return response()->json([
            'data' => $board,
            'mensaje' => 'Tablero obtenido correctamente.'
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/boards/{id}",
     *     summary="Actualizar un tablero",
     *     tags={"Boards"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tablero actualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="nombre", type="string", example="Proyecto Ciencias Sociales"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tablero actualizado correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No autorizado para actualizar este tablero.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $board = Board::findOrFail($id);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para actualizar este tablero.'
            ], 403);
        }
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);
        $board->update($data);
        return response()->json([
            'data' => $board,
            'mensaje' => 'Tablero actualizado correctamente.'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/boards/{id}",
     *     summary="Eliminar un tablero",
     *     tags={"Boards"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tablero eliminado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example=null),
     *             @OA\Property(property="mensaje", type="string", example="Tablero eliminado correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="No autorizado para eliminar este tablero.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $board = Board::findOrFail($id);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para eliminar este tablero.'
            ], 403);
        }
        $board->delete();
        return response()->json([
            'data' => null,
            'mensaje' => 'Tablero eliminado correctamente.'
        ], 200);
    }
}
