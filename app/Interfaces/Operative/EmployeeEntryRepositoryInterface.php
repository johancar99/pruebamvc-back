<?php

namespace App\Interfaces\Operative;

use App\Interfaces\RepositoryInterface;

interface EmployeeEntryRepositoryInterface extends RepositoryInterface
{
    /**
     * Store a new EmployeeEntry record.
     *
     * @param array $data Data to store for the EmployeeEntry.
     * @return mixed
     */
    public function store(array $data);
}
