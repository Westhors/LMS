<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'name_ar' => $this->name_ar,
            'description_ar' => $this->description_ar,

            'facebook_meta' => $this->facebook_meta,
            'google_meta' => $this->google_meta,
            'tiktok_meta' => $this->tiktok_meta,
            'you_tube_meta' => $this->you_tube_meta,

            'facebook_count' => $this->facebook_count,
            'google_count' => $this->google_count,
            'tiktok_count' => $this->tiktok_count,
            'you_tube_count' => $this->you_tube_count,


            'active' => $this->active,
            'teacher_id' => $this->teacher_id,

            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
