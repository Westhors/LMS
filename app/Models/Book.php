<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'enrollments',
            'book_id',
            'student_id'
        )->where('type', 'book');
    }
}

