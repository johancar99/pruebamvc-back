<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerWrapper;
use App\Repositories\System\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    // Listado de usuarios
    public function index(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            $search = $request->input('search', '');
            $filter = $request->input('filter', 1);
            $perPage = $request->input('perPage', 15);

            $users = $this->user_repository->search($search, $filter==3?null:$filter, $perPage);

            return [
                'data' => $users,
                'message' => 'Usuarios obtenidos correctamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'No se pudieron obtener los usuarios.'
        ]);
    }


    // Crear un nuevo usuario
    public function store(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            // Validación dentro del wrapper
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            // Encriptamos la contraseña
            $data['password'] = bcrypt($data['password']);

            $user = $this->user_repository->setUser(Auth::user())->store($data)->getModel();
            $user->assignRole('admin_room_911');

            // Devolver el usuario creado
            return [
                'data' => $user,
                'message' => 'Usuario creado correctamente',
                'code' => 201
            ];
        }, [
            'state' => false,
            'error' => 'Error al crear el usuario'
        ]);
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        return ControllerWrapper::execApiResponse(function () use ($id) {
            $user = $this->user_repository->findAndGet($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado', 404);
            }
            return [
                'data' => $user,
                'message' => 'Usuario encontrado',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al obtener el usuario.'
        ]);
    }

    // Actualizar un usuario
    public function update(Request $request, $id)
    {
        return ControllerWrapper::execApiResponse(function () use ($request, $id) {
            // Validación de datos (solo se actualizan los campos que se reciben)
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => "required|email|unique:users,email,{$id}"
            ]);

            $user = $this->user_repository->findAndGet($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado', 404);
            }


            $new_user = $user->getRepository()->setUser(Auth::user())->update($data)->getModel();
            return [
                'data' => $new_user,
                'message' => 'Usuario actualizado exitosamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al actualizar el usuario'
        ]);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        return ControllerWrapper::execApiResponse(function () use ($id) {
            $user = $this->user_repository->findAndGet($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado', 404);
            }
            // Eliminar el usuario utilizando el repositorio
            $user->getRepository()->setUser(Auth::user())->delete();

            return [
                'data' => null,
                'message' => 'Usuario eliminado exitosamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al eliminar el usuario.'
        ]);
    }

    public function updateStatus(Request $request, $id){
        return ControllerWrapper::execApiResponse(function () use ($request, $id) {
            $data = $request->validate([
                'active'     => 'required'
            ]);
            $user = $this->user_repository->findAndGet($id);
            if (!$user) {
                throw new \Exception('Usuario no encontrado', 404);
            }
            $user->getRepository()->setUser(Auth::user())->update([
                "active"=>!$data["active"]
            ]);

            return [
                'data' => null,
                'message' => 'Estado actualizado correctamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al actualizar el estado'
        ]);
    }
}
