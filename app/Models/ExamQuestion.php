<?php

namespace App\Models;

use App\Traits\HasMedia;

class ExamQuestion extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];


    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'question_id');
    }
    
    public function question_image()
    {
        return $this->belongsToMany(
            Media::class,
            'mediable',
            'model_id',
            'media_id'
        )
            ->wherePivot('model_type', self::class)
            ->wherePivot('collection', 'question_image');
    }
}
