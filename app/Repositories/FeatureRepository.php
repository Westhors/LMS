<?php

namespace App\Repositories;

use App\Interfaces\FeatureRepositoryInterface;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;

class FeatureRepository extends CrudRepository implements FeatureRepositoryInterface
{
    protected Model $model;

    public function __construct(Feature $model)
    {
        $this->model = $model;
    }
}
