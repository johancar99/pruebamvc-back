<?php

namespace App\Repositories\System;

use App\Interfaces\System\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Log;

class UserRepository extends Repository implements UserRepositoryInterface
{

    /**
     * Get a new instance of the model.
     *
     * @return User
     */
    public function getNewModel()
    {
        return new User();
    }

    /**
     * Store a new user record.
     *
     * @param array $data Data to store for the user.
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
     * Update an existing user record.
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
     * Delete the user record.
     *
     * @return self
     */
    public function delete()
    {
        $this->preDelete();
        return $this;
    }

    /**
     * Filtra y pagina los registros de empresas.
     *
     * @param string $search Cadena de búsqueda.
     * @param string $filter Filtro de estado.
     * @param int $perPage Número de registros por página.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($search, $filter, $perPage)
    {
        // Creamos una nueva consulta sobre el modelo.
        $query = $this->newQuery();

        // Aplicar búsqueda por nombre o email si se proporcionó una cadena.
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }


        if($filter!=null){
            $query->where('active', $filter);
        }


        // Retornamos el resultado paginado.
        return $query->paginate($perPage);
    }
}
