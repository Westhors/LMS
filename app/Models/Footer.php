<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Footer extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

}

