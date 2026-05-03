<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'name_ar' => $this->name_ar ?? null,
            'active' => $this->active ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

