<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\Column;

class ColumnController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/boards/{boardId}/columns",
     *     summary="Listar columnas de un tablero",
     *     tags={"Columns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de columnas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="board_id", type="integer", example=1),
     *                     @OA\Property(property="nombre", type="string", example="Para investigar"),
     *                     @OA\Property(property="orden", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Columnas obtenidas correctamente.")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para ver las columnas de este tablero.'
            ], 403);
        }
        $columns = $board->columns()->orderBy('orden')->get();
        return response()->json([
            'data' => $columns,
            'mensaje' => 'Columnas obtenidas correctamente.'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/boards/{boardId}/columns",
     *     summary="Crear una columna en un tablero",
     *     tags={"Columns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Para investigar"),
     *             @OA\Property(property="orden", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Columna creada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Para investigar"),
     *                 @OA\Property(property="orden", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Columna creada exitosamente.")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para crear columnas en este tablero.'
            ], 403);
        }
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'orden' => 'integer',
        ]);
        $column = $board->columns()->create($data);
        return response()->json([
            'data' => $column,
            'mensaje' => 'Columna creada exitosamente.'
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/boards/{boardId}/columns/{columnId}",
     *     summary="Actualizar una columna",
     *     tags={"Columns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="columnId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Investigando"),
     *             @OA\Property(property="orden", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Columna actualizada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Investigando"),
     *                 @OA\Property(property="orden", type="integer", example=2),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Columna actualizada correctamente.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $boardId, $columnId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para actualizar columnas en este tablero.'
            ], 403);
        }
        $column = $board->columns()->findOrFail($columnId);
        $data = $request->validate([
            'nombre' => 'string|max:255',
            'orden' => 'integer',
        ]);
        $column->update($data);
        return response()->json([
            'data' => $column,
            'mensaje' => 'Columna actualizada correctamente.'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/boards/{boardId}/columns/{columnId}",
     *     summary="Eliminar una columna",
     *     tags={"Columns"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="columnId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Columna eliminada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example=null),
     *             @OA\Property(property="mensaje", type="string", example="Columna eliminada correctamente.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $boardId, $columnId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para eliminar columnas en este tablero.'
            ], 403);
        }
        $column = $board->columns()->findOrFail($columnId);
        $column->delete();
        return response()->json([
            'data' => null,
            'mensaje' => 'Columna eliminada correctamente.'
        ], 200);
    }
}
