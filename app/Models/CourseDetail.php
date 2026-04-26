<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseDetail extends BaseModel
{
    protected $table = 'course_details';

    use HasFactory;

    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }


}
