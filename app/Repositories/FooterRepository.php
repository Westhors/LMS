<?php

namespace App\Repositories;

use App\Interfaces\FooterRepositoryInterface;
use App\Models\Footer;
use Illuminate\Database\Eloquent\Model;

class FooterRepository extends CrudRepository implements FooterRepositoryInterface
{
    protected Model $model;

    public function __construct(Footer $model)
    {
        $this->model = $model;
    }
}
