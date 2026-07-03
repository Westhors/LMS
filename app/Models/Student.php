<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Foundation\Auth\User as Authenticatable; // 🔥 مهم
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ,HasMedia , SoftDeletes;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'device_blocked' => 'boolean'
    ];
    public function courseDetailViews()
    {
        return $this->hasMany(CourseDetailView::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }


    public function centerHour()
    {
        return $this->belongsTo(CenterHour::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function lessonAttendances()
    {
        return $this->hasMany(
            CourseDetailAttendance::class
        );
    }
}
