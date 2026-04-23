<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
class Teacher extends BaseModel
{
    use HasFactory,Authenticatable,HasApiTokens;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function stages()
    {
        return $this->belongsToMany(Stage::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function assistantTeachers()
    {
        return $this->hasMany(AssistantTeacher::class);
    }
public function teacherImage()
{
    return $this->hasOne(Media::class, 'id', 'id')
        ->whereIn('id', function ($q) {
            $q->select('media_id')
              ->from('mediable')
              ->where('teacher_id', $this->id)
              ->where('collection', 'teacher_image');
        });
}
}

