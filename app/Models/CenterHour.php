<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class CenterHour extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];


    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

}

