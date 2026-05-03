<?php

namespace App\Models;

class Semester extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
