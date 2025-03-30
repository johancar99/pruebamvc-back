<?php

namespace App\Interfaces\System;

use App\Interfaces\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Store a new user record.
     *
     * @param array $data Data to store for the user.
     * @return mixed
     */
    public function store(array $data);

    /**
     * Update an existing user record.
     *
     * @param array $data Data to update for the user.
     * @return mixed
     */
    public function update(array $data);

    /**
     * Delete the user record.
     *
     * @return mixed
     */
    public function delete();

    /**
     * Filtra y pagina los registros de usuarios.
     *
     * @param string $search Cadena de búsqueda.
     * @param string $filter Filtro de estado.
     * @param int $perPage Número de registros por página.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($search, $filter, $perPage);
}
