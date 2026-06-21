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
            'facebook_meta' => $this->facebook_meta ?? null,
            'google_meta' => $this->google_meta ?? null,
            'tiktok_meta' => $this->tiktok_meta ?? null,
            'you_tube_meta' => $this->you_tube_meta ?? null,


            'facebook_count' => $this->facebook_count ?? null,
            'google_count' => $this->google_count ?? null,
            'tiktok_count' => $this->tiktok_count ?? null,
            'you_tube_count' => $this->you_tube_count ?? null,





            'active' => $this->active ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
