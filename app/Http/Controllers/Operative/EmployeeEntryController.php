<?php

namespace App\Http\Controllers\Operative;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerWrapper;
use App\Models\Operative\EmployeeEntry;
use App\Repositories\Operative\EmployeeEntryRepository;
use App\Repositories\Operative\EmployeeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeEntryController extends Controller
{
    protected $employee_entry_repository;
    protected $employee_repository;

    public function __construct(EmployeeEntryRepository $employee_entry_repository, EmployeeRepository $employee_repository)
    {
        $this->employee_entry_repository = $employee_entry_repository;
        $this->employee_repository = $employee_repository;
    }

    public function store(Request $request)
    {
        return ControllerWrapper::execApiResponse(function () use ($request) {
            // Validación dentro del wrapper
            $data = $request->validate([
                'document'    => 'required',
            ]);


            $employee = $this->employee_repository->getByDocument($data["document"]);

            if(!$employee){
                throw new \Exception('Empleado no encontrado', 404);
            }

            $wasSuccessful = $employee->access;

            $employee_entry = EmployeeEntry::create([
                'employee_id' => $employee->id,
                'entry_time' => Carbon::now(),
                'was_successful' => $wasSuccessful,
            ]);

            // Devolver el empleado creado
            return [
                'data' => $employee_entry,
                'message' => 'Ingreso creado correctamente',
                'code' => 201
            ];
        }, [
            'state' => false,
            'error' => 'Error al crear el usuario'
        ]);
    }

}
