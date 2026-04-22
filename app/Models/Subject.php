<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Subject extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

}

