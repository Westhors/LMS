<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssistantTeacherResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name   ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'active' => $this->active ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
