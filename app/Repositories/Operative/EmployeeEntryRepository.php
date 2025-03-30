<?php

namespace App\Repositories\Operative;

use App\Interfaces\Operative\EmployeeEntryRepositoryInterface;
use App\Models\Operative\EmployeeEntry;
use App\Repositories\Repository;

class EmployeeEntryRepository extends Repository implements EmployeeEntryRepositoryInterface
{

    public function store(array $data)
    {
        $this->setNewModel();
        $this->fillFromArray($data);
        $this->preCreate();
        return $this;
    }

    public function getNewModel()
    {
        return new EmployeeEntry();
    }
}
