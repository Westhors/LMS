<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'name_ar' => $this->name_ar ?? null,
            'description_ar' => $this->description_ar ?? null,
            'active' => $this->active ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
