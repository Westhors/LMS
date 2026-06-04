<?php

namespace App\Models;

use App\Traits\HasMedia;

class Semester extends BaseModel
{
    protected $guarded = ['id'];
    use HasMedia;

    protected $with = [
        'media',
    ];
    protected $casts = [
        'active' => 'boolean'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'enrollments',
            'semester_id',
            'student_id'
        )
        ->where('type', 'semester');
    }
}
