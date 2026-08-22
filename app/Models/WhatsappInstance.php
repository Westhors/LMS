<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    protected $fillable = [
        'teacher_id',
        'instance_id',
        'access_token',
        'phone',
        'status',
    ];

    protected $hidden = [
        'access_token',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
