<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends BaseModel
{
    use HasFactory;

    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    // public function students()
    // {
    //     return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id');
    // }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->withDefault();
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function details()
    {
        return $this->hasMany(CourseDetail::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'enrollments',
            'course_id',
            'student_id'
        )->wherePivot('type', 'course');
    }
}
