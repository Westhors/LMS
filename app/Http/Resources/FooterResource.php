<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FooterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'facebook_link' => $this->facebook_link,
            'youtube_link' => $this->youtube_link,
            'instagram_link' => $this->instagram_link,
            'tiktok_link' => $this->tiktok_link,
            'whatsapp_link' => $this->whatsapp_link,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'active' => $this->active,
            'teacher_id' => $this->teacher_id,
        ];
    }
}
