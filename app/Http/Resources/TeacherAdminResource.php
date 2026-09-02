<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherAdminResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'type' => "teacher",
            'role' => 'teacher',
            'expire_date' => $this->expire_date,
            'show_expire_message' => (bool) $this->show_expire_message,
        ];
    }
}
