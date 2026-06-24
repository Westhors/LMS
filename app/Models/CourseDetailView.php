<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDetailView extends Model
{
    protected $guarded = ['id'];

    public function courseDetail(): BelongsTo
    {
        return $this->belongsTo(CourseDetail::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
