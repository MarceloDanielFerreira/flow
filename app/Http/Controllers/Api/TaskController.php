<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Board;
use App\Models\Task;
use App\Models\Column;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/boards/{boardId}/tasks",
     *     summary="Listar tareas de un tablero",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tareas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="board_id", type="integer", example=1),
     *                     @OA\Property(property="column_id", type="integer", example=2),
     *                     @OA\Property(property="titulo", type="string", example="Investigar tema 1"),
     *                     @OA\Property(property="descripcion", type="string", example="Buscar información sobre el tema 1"),
     *                     @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tareas obtenidas correctamente.")
     *         )
     *     )
     * )
     */
    public function index(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para ver las tareas de este tablero.'
            ], 403);
        }
        $tasks = $board->tasks()->with('column')->get();
        return response()->json([
            'data' => $tasks,
            'mensaje' => 'Tareas obtenidas correctamente.'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/boards/{boardId}/tasks",
     *     summary="Crear una tarea en un tablero",
     *     tags={"Tasks"},
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
     *             required={"titulo", "column_id"},
     *             @OA\Property(property="titulo", type="string", example="Investigar tema 1"),
     *             @OA\Property(property="descripcion", type="string", example="Buscar información sobre el tema 1"),
     *             @OA\Property(property="column_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tarea creada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="column_id", type="integer", example=2),
     *                 @OA\Property(property="titulo", type="string", example="Investigar tema 1"),
     *                 @OA\Property(property="descripcion", type="string", example="Buscar información sobre el tema 1"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tarea creada exitosamente.")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para crear tareas en este tablero.'
            ], 403);
        }
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'column_id' => 'required|exists:columns,id',
        ]);
        // Verifica que la columna pertenezca al tablero
        $column = $board->columns()->findOrFail($data['column_id']);
        $task = $board->tasks()->create($data);
        return response()->json([
            'data' => $task,
            'mensaje' => 'Tarea creada exitosamente.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/boards/{boardId}/tasks/{taskId}",
     *     summary="Actualizar una tarea",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="titulo", type="string", example="Nuevo título"),
     *             @OA\Property(property="descripcion", type="string", example="Nueva descripción"),
     *             @OA\Property(property="column_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarea actualizada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="board_id", type="integer", example=1),
     *                 @OA\Property(property="column_id", type="integer", example=2),
     *                 @OA\Property(property="titulo", type="string", example="Nuevo título"),
     *                 @OA\Property(property="descripcion", type="string", example="Nueva descripción"),
     *                 @OA\Property(property="created_at", type="string", example="2024-07-08T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-07-08T12:00:00Z")
     *             ),
     *             @OA\Property(property="mensaje", type="string", example="Tarea actualizada correctamente.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $boardId, $taskId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para actualizar tareas en este tablero.'
            ], 403);
        }
        $task = $board->tasks()->findOrFail($taskId);
        $data = $request->validate([
            'titulo' => 'string|max:255',
            'descripcion' => 'nullable|string',
            'column_id' => 'exists:columns,id',
        ]);
        // Si se quiere mover de columna, verifica que la columna pertenezca al tablero
        if (isset($data['column_id'])) {
            $column = $board->columns()->findOrFail($data['column_id']);
        }
        $task->update($data);
        return response()->json([
            'data' => $task,
            'mensaje' => 'Tarea actualizada correctamente.'
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/boards/{boardId}/tasks/{taskId}",
     *     summary="Eliminar una tarea",
     *     tags={"Tasks"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="boardId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarea eliminada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example=null),
     *             @OA\Property(property="mensaje", type="string", example="Tarea eliminada correctamente.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $boardId, $taskId)
    {
        $board = Board::findOrFail($boardId);
        if ($board->user_id !== $request->user()->id) {
            return response()->json([
                'mensaje' => 'No autorizado para eliminar tareas en este tablero.'
            ], 403);
        }
        $task = $board->tasks()->findOrFail($taskId);
        $task->delete();
        return response()->json([
            'data' => null,
            'mensaje' => 'Tarea eliminada correctamente.'
        ], 200);
    }
}
