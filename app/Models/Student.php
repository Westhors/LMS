<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends BaseModel
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

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
