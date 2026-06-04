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
            'hours_start' => $this->hours_start ?? null,
            'hours_end' => $this->hours_end ?? null,
            'address' => $this->address ?? null,
            'note' => $this->note ?? null,
            'teacher_id' => $this->teacher_id ?? null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

