<?php

namespace App\Repositories;

use App\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;

class ExamRepository extends CrudRepository implements ExamRepositoryInterface
{
    protected Model $model;

    public function __construct(Exam $model)
    {
        $this->model = $model;
    }
}
