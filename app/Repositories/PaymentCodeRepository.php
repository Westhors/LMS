<?php

namespace App\Repositories;

use App\Interfaces\PaymentCodeRepositoryInterface;
use App\Models\PaymentCode;
use Illuminate\Database\Eloquent\Model;

class PaymentCodeRepository extends CrudRepository implements PaymentCodeRepositoryInterface
{
    protected Model $model;

    public function __construct(PaymentCode $model)
    {
        $this->model = $model;
    }
}
