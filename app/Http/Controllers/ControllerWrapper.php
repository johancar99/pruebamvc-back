<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ControllerWrapper
{
    /**
     * Wrapper para manejar las respuestas de la API, controlando errores comunes.
     * @param callable $callback - Función de la acción que se va a ejecutar.
     * @param array $response_error - Respuesta de error predeterminada.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function execApiResponse($callback, array $response_error)
    {
        try {
            // Iniciar una transacción para asegurar la consistencia de la base de datos
            DB::beginTransaction();

            // Ejecutar el callback y capturar la respuesta
            $response = $callback();


            // Asegurarse de que la respuesta incluya un campo 'state' que indique éxito
            $response = array_merge($response, [
                'state' => true,
            ]);

            // Confirmar la transacción si todo ha salido bien
            DB::commit();

            // Devolver la respuesta exitosa con el código correspondiente
            return response()->json($response, $response['code']);

        } catch (ValidationException $e) {
            // Reportar y manejar el error de validación
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Validation error: ' . $e->getMessage(),
            ]);
            return response()->json($response, 403); // 403 para errores de validación

        } catch (QueryException $e) {
            // Reportar y manejar el error de consulta a la base de datos
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Database query error: ' . $e->getMessage(),
            ]);
            return response()->json($response, 500); // 500 para errores del servidor

        } catch (ModelNotFoundException $e) {
            // Reportar y manejar el error cuando no se encuentra un recurso
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Resource not found: ' . $e->getMessage(),
            ]);
            return response()->json($response, 404); // 404 cuando el recurso no se encuentra

        } catch (AuthorizationException $e) {
            // Reportar y manejar el error de autorización
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Unauthorized: ' . $e->getMessage(),
            ]);
            return response()->json($response, 403); // 403 para errores de autorización

        } catch (MethodNotAllowedHttpException $e) {
            // Reportar y manejar el error cuando el método no es permitido
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Method not allowed: ' . $e->getMessage(),
            ]);
            return response()->json($response, 405); // 405 cuando el método no es permitido

        } catch (ThrottleRequestsException $e) {
            // Reportar y manejar el error de exceso de solicitudes
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'Too many requests: ' . $e->getMessage(),
            ]);
            return response()->json($response, 429); // 429 para demasiadas solicitudes

        } catch (\Exception $e) {
            // Reportar y manejar cualquier otro error genérico
            report($e);
            DB::rollBack();
            $response = array_merge($response_error, [
                'state' => false,
                'error' => 'An error occurred: ' . $e->getMessage(),
            ]);
            return response()->json($response, 500); // 500 para errores genéricos
        }
    }

    /**
     * Wrapper para devolver una respuesta de éxito estándar.
     *
     * @param array $data - Los datos que se devolverán como parte de la respuesta
     * @param string $message - El mensaje de éxito
     * @param int $status - El código de estado HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data, $message = 'Success', $status = 200)
    {
        return response()->json([
            'state' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Wrapper para devolver una respuesta de error estándar.
     *
     * @param string $message - El mensaje de error
     * @param int $status - El código de estado HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message, $status)
    {
        return response()->json([
            'state' => false,
            'error' => $message
        ], $status);
    }
}
