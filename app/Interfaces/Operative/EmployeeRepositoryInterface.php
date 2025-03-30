<?php

namespace App\Interfaces\Operative;

use App\Interfaces\RepositoryInterface;

interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * Store a new user record.
     *
     * @param array $data Data to store for the employee.
     * @return mixed
     */
    public function store(array $data);

    /**
     * Update an existing user record.
     *
     * @param array $data Data to update for the employee.
     * @return mixed
     */
    public function update(array $data);

    /**
     * Delete the employee record.
     *
     * @return mixed
     */
    public function delete();

    public function search($search, $filter, $perPage);

    public function getByDocument($document);
}
