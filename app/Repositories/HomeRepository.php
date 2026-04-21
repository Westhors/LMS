<?php

namespace App\Repositories;

use App\Interfaces\HomeRepositoryInterface;
use App\Models\Home;
use Illuminate\Database\Eloquent\Model;

class HomeRepository extends CrudRepository implements HomeRepositoryInterface
{
    protected Model $model;

    public function __construct(Home $model)
    {
        $this->model = $model;
    }
}
