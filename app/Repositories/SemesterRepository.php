<?php

namespace App\Repositories;

use App\Interfaces\SemesterRepositoryInterface;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;

class SemesterRepository extends CrudRepository implements SemesterRepositoryInterface
{
    protected Model $model;

    public function __construct(Semester $model)
    {
        $this->model = $model;
    }
}
