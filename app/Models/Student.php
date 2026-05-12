<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // 🔥 مهم
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

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
