<?php

namespace App\Models;

use App\Traits\HasMedia;

class ExamAnswer extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

     protected $casts = [
        'is_auto_corrected' => 'boolean',
        'is_correct' => 'boolean'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
