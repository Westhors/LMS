<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherDashboardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? null,
            'email' => $this->email ?? null,
            'sub_domain' => $this->sub_domain ?? null,
            'second_sub_domain' => $this->second_sub_domain ?? null,
            'phone' => $this->phone ?? null,
            'active' => $this->active ?? null,
            'theme' => $this->theme ?? null,
            'backgroud_color' => $this->backgroud_color ?? null,
            'font_color' => $this->font_color ?? null,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'expire_date' => $this->expire_date ?? null,
            'logoUrl' => $this->getFirstMediaUrl('logo'),
            'logo' => new MediaResource($this->getFirstMedia('logo')),
            'createdAt' => $this->created_at?->format('d F, Y'),
        ];
    }
}
