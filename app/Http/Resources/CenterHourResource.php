<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CenterHourResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? null,
            'date' => $this->date ?? null,
            'hours' => $this->hours ?? null,
            'note' => $this->note ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

