<?php

namespace App\Repositories;

use App\Interfaces\CenterHourRepositoryInterface;
use App\Models\CenterHour;
use Illuminate\Database\Eloquent\Model;

class CenterHourRepository extends CrudRepository implements CenterHourRepositoryInterface
{
    protected Model $model;

    public function __construct(CenterHour $model)
    {
        $this->model = $model;
    }
}
