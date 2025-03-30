<?php

namespace App\Http\Controllers\Operative;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerWrapper;
use App\Repositories\Operative\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    protected $employee_repository;

    public function __construct(EmployeeRepository $employee_repository)
    {
        $this->employee_repository = $employee_repository;
    }

    // Listado de empleados
    public function index(Request $request)
    {
        Log::info($request);
        return ControllerWrapper::execApiResponse(function () use ($request) {
            $search = $request->input('search', '');
            $filter = $request->input('filter', '');
            $perPage = $request->input('perPage', 15);
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->startOfDay()->toDateTimeString();
                $endDate = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
            }

            $employees = $this->employee_repository->search(
                $search,
                $filter == 3 ? null : $filter,
                $perPage,
                $startDate,
                $endDate
            );

            return [
                'data' => $employees,
                'message' => 'Empleados obtenidos correctamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'No se pudieron obtener los Empleados.'
        ]);
    }

    // Crear un nuevo empleado
    public function store(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            // Validación dentro del wrapper
            $data = $request->validate([
                'document'    => 'required|unique:employees,document',
                'first_name'  => 'required',
                'last_name'   => 'required',
                'department'  => 'required',
                'access'      => 'required|boolean',
            ]);


            $employee = $this->employee_repository->setUser(Auth::user())->store($data)->getModel();

            // Devolver el empleado creado
            return [
                'data' => $employee,
                'message' => 'Empleado creado correctamente',
                'code' => 201
            ];
        }, [
            'state' => false,
            'error' => 'Error al crear el usuario'
        ]);
    }

    // Mostrar un empleado específico
    public function show($id)
    {
        return ControllerWrapper::execApiResponse(function () use ($id) {
            $employee = $this->employee_repository->findAndGet($id);
            if (!$employee) {
                // Lanza excepción para que el wrapper la capture
                throw new \Exception('Empleado no encontrado', 404);
            }
            return [
                'data' => $employee,
                'message' => 'Empleado encontrado',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al obtener el usuario.'
        ]);
    }

    // Actualizar un empleado
    public function update(Request $request, $id)
    {
        return ControllerWrapper::execApiResponse(function () use ($request, $id) {
            // Validación de datos (solo se actualizan los campos que se reciben)
            $data = $request->validate([
                'document'    => 'required|unique:employees,document,'.$id,
                'first_name'  => 'required',
                'last_name'   => 'required',
                'department'  => 'required',
                'access'      => 'required|boolean',
            ]);

            $employee = $this->employee_repository->findAndGet($id);
            if (!$employee) {
                throw new \Exception('Empleado no encontrado', 404);
            }

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $new_employee = $employee->getRepository()->setUser(Auth::user())->update($data)->getModel();
            return [
                'data' => $new_employee,
                'message' => 'Empleado actualizado exitosamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al actualizar el empleado'
        ]);
    }

    // Eliminar un empleado
    public function destroy($id)
    {
        return ControllerWrapper::execApiResponse(function () use ($id) {
            $employee = $this->employee_repository->findAndGet($id);
            if (!$employee) {
                throw new \Exception('Empleado no encontrado', 404);
            }
            // Eliminar el hotel utilizando el repositorio
            $employee->getRepository()->setUser(Auth::user())->delete();

            return [
                'data' => null,
                'message' => 'Empleado eliminado exitosamente',
                'code' => 200
            ];
        }, [
            'state' => false,
            'error' => 'Error al eliminar el empleado.'
        ]);
    }

    public function import(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {

            Log::info($request->data);

            foreach ($request->data as $data){
                if (
                    !empty($data['document']) &&
                    !empty($data['first_name']) &&
                    !empty($data['last_name']) &&
                    !empty($data['department'])
                ) {
                    (new EmployeeRepository())->setUser(Auth::user())->store([
                        'document'    => $data['document'],
                        'first_name'  => $data['first_name'],
                        'last_name'   => $data['last_name'],
                        'department'  => $data['department'],
                        'access'      => true,
                    ]);
                }
            }

            // Devolver el empleado creado
            return [
                'data' => null,
                'message' => 'Empleado creado correctamente',
                'code' => 201
            ];
        }, [
            'state' => false,
            'error' => 'Error al crear el usuario'
        ]);
    }
}
