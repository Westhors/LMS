<?php

namespace App\Repositories;

use App\Interfaces\AboutRepositoryInterface;
use App\Models\About;
use Illuminate\Database\Eloquent\Model;

class AboutRepository extends CrudRepository implements AboutRepositoryInterface
{
    protected Model $model;

    public function __construct(About $model)
    {
        $this->model = $model;
    }
}
