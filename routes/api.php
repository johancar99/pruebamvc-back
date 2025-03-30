<?php

use App\Http\Controllers\Operative\EmployeeController;
use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\System\UserController;
use Illuminate\Support\Facades\Route;

// Ruta pública para login
Route::post('login', [AuthController::class, 'login']);

Route::group(['prefix' => 'entries'], function () {
    Route::post('/', [\App\Http\Controllers\Operative\EmployeeEntryController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'users'], function () {

        Route::post('/', [UserController::class, 'store']); // Ruta para crear un nuevo usuario

        Route::get('/', [UserController::class, 'index']); // Ruta para obtener todos los usuarios

        Route::get('{id}', [UserController::class, 'show']); // Ruta para obtener un usuario específico

        Route::put('{id}', [UserController::class, 'update']); // Ruta para actualizar un usuario

        Route::put('/update-status/{id}', [UserController::class, 'updateStatus']); // Ruta para actualizar un usuario

        Route::delete('{id}', [UserController::class, 'destroy']); // Ruta para eliminar un usuario
    });

    Route::group(['prefix' => 'employees'], function () {

        Route::post('/', [EmployeeController::class, 'store']); // Ruta para crear un nuevo empleado

        Route::get('/', [EmployeeController::class, 'index']); // Ruta para obtener todos los empleados

        Route::get('{id}', [EmployeeController::class, 'show']); // Ruta para obtener un empleado específico

        Route::put('{id}', [EmployeeController::class, 'update']); // Ruta para actualizar un empleado

        Route::put('/update-access/{id}', [EmployeeController::class, 'updateAccess']); // Ruta para actualizar el estado de un empleado

        Route::delete('{id}', [EmployeeController::class, 'destroy']); // Ruta para eliminar un emplead
    });

});
