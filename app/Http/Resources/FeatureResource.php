<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'active' => $this->active ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
