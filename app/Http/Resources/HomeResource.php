<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'sub_title' => $this->sub_title ?? null,
            'description' => $this->description ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'active' => $this->active ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
