<?php

namespace App\Models\Operative;

use App\Repositories\Operative\EmployeeRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $with = ["entries"];

    public function getRepository(): EmployeeRepository
    {
        if(is_null($this->repository)){
            $repo= new EmployeeRepository();
            $this->repository= $repo->setModel($this);
        }
        return $this->repository;
    }

    public function entries()
    {
        return $this->hasMany(EmployeeEntry::class);
    }
}
