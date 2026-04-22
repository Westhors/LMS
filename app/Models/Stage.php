<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Stage extends BaseModel
{
    use HasFactory;

    use HasMedia;
    
    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }
}

