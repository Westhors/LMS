<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $guarded = ['id'];

    public function options()
    {
        return $this->hasMany(
            QuestionBankOption::class,
            'question_bank_id'
        );
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
            ->wherePivot('collection', 'question_bank_image');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function courseDetail()
    {
        return $this->belongsTo(CourseDetail::class);
    }
}
