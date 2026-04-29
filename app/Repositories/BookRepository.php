<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Model;

class BookRepository extends CrudRepository implements BookRepositoryInterface
{
    protected Model $model;

    public function __construct(Book $model)
    {
        $this->model = $model;
    }
}
