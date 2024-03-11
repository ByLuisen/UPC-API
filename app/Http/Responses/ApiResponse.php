<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $message = 'Operación exitosa', $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error($message = 'Error en la operación', $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    public static function validationError($errors, $message = 'Error de validación', $status = 422): JsonResponse
    {
        return response()->json([
            'status' => 'validation_error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public static function noRoles($message = 'No tienes los roles requeridos.', $status = 403): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $status);
    }

    public static function notFound($message = 'Recurso no encontrado', $status = 404): JsonResponse
    {
        return response()->json([
            'status' => 'not_found',
            'message' => $message,
        ], $status);
    }

    public static function unauthorized($message = 'No autorizado', $status = 401): JsonResponse
    {
        return response()->json([
            'status' => 'unauthorized',
            'message' => $message,
        ], $status);
    }

    public static function deleted($message = 'Recurso eliminado exitosamente', $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], $status);
    }

    // Puedes agregar más métodos según tus necesidades.
}
