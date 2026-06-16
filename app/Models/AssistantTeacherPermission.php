<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantTeacherPermission extends Model
{
    protected $guarded = ['id'];


    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public function assistantTeacher()
    {
        return $this->belongsTo(AssistantTeacher::class);
    }
}
