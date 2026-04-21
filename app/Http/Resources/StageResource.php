<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position ?? null,
            'active' => $this->active ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}


