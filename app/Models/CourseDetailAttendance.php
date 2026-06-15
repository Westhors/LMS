<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDetailAttendance extends Model
{
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class);
    }
}
