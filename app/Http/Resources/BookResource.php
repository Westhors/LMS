<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'writer' => $this->writer ?? null,
            'active' => $this->active ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'price' => $this->price ?? null,
            'pages_count' => $this->pages_count ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
