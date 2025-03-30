<?php

namespace App\Models\Operative;

use App\Repositories\Operative\EmployeeEntryRepository;
use Illuminate\Database\Eloquent\Model;

class EmployeeEntry extends Model
{
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRepository(): EmployeeEntryRepository
    {
        if(is_null($this->repository)){
            $repo= new EmployeeEntryRepository();
            $this->repository= $repo->setModel($this);
        }
        return $this->repository;
    }
}
