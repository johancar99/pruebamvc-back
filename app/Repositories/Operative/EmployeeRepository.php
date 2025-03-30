<?php

namespace App\Repositories\Operative;

use App\Interfaces\Operative\EmployeeRepositoryInterface;
use App\Models\Operative\Employee;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Log;

class EmployeeRepository extends Repository implements EmployeeRepositoryInterface
{

    /**
     * Get a new instance of the model.
     *
     * @return Employee
     */
    public function getNewModel()
    {
        return new Employee();
    }

    /**
     * Store a new user record.
     *
     * @param array $data Data to store for the employee.
     * @return self
     */
    public function store(array $data)
    {
        $this->setNewModel();
        $this->fillFromArray($data);
        $this->preCreate();
        return $this;
    }

    /**
     * Update an existing employee record.
     *
     * @param array $data Data to update for the user.
     * @return self
     */
    public function update(array $data)
    {
        $this->fillFromArray($data);
        $this->preUpdate();
        return $this;
    }

    /**
     * Delete the employee record.
     *
     * @return self
     */
    public function delete()
    {
        $this->preDelete();
        return $this;
    }

    /**
     * Filtra y pagina los registros de empleados.
     *
     * @param string $search Cadena de búsqueda.
     * @param string|null $filter Filtro de estado de acceso.
     * @param int $perPage Número de registros por página.
     * @param string|null $startDate Fecha de inicio del filtro.
     * @param string|null $endDate Fecha de fin del filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($search, $filter, $perPage, $startDate = null, $endDate = null)
    {

        // Creamos una nueva consulta sobre el modelo Employee.
        $query = $this->newQuery();

        // Búsqueda por nombre, apellido o documento
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%");
            });
        }

        // Filtro de acceso
        if (!empty($filter)) {
            $query->where('department', $filter);
        }

        // Filtro por fechas usando la relación 'entries'
        if (!empty($startDate) && !empty($endDate)) {
            $query->whereHas('entries', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_time', [$startDate, $endDate]);
            });

            // Opcionalmente cargamos solo las entries en ese rango
            $query->with(['entries' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_time', [$startDate, $endDate]);
            }]);
        }

        // Retornamos los resultados paginados
        return $query->paginate($perPage);
    }

    public function getByDocument($document){
        return $this->getNewModel()->where("document", $document)->first();
    }
}
