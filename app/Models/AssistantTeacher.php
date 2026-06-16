<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class AssistantTeacher extends BaseModel
{
    use HasFactory,Authenticatable,HasApiTokens;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'assistant_teacher_permissions')
            ->withPivot(['view', 'create', 'update', 'delete']);
    }

}

