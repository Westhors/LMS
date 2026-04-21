<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name   ?? null,
            'email' => $this->email ?? null,
            'sub_domain' => $this->sub_domain ?? null,
            'phone' => $this->phone ?? null,
            'active' => $this->active ?? null,
            'stage' => new StageResource($this->stage),
            'subject' => new SubjectResource($this->subject),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

