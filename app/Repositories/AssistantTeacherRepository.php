<?php

namespace App\Repositories;

use App\Interfaces\AssistantTeacherRepositoryInterface;
use App\Models\AssistantTeacher;
use Illuminate\Database\Eloquent\Model;

class AssistantTeacherRepository extends CrudRepository implements AssistantTeacherRepositoryInterface
{
    protected Model $model;

    public function __construct(AssistantTeacher $model)
    {
        $this->model = $model;
    }
}
