<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Exam extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
