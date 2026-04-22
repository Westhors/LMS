<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class About extends BaseModel
{
    use HasMedia;

    protected $with = [
        'media',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

}

