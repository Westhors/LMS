<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];


    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'question_id');
    }

}
