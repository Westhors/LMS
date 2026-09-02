<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonNote extends Model
{
    protected $table = 'lesson_notes';

    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class, 'course_detail_id');
    }
}
