<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_parent' => $this->phone_parent,
            'code_parent' => $this->code_parent,
            'barcode' => $this->barcode,
            'type_of_attendance' => $this->type_of_attendance,
            'gender' => $this->gender,
            'active' => $this->active,
            'balance' => $this->balance ?? 0,
            'governorate' => $this->governorate,
            'school_name' => $this->school_name,
            'type_of_study' => $this->type_of_study,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'teacher_id' => $this->teacher_id,
            'stage_id' => $this->stage_id,
            'stage' => $this->whenLoaded('stage'),
            'center_hour_id' => $this->center_hour_id,
            'joined_at' => optional($this->pivot?->created_at)
                ->format('d F, Y h:i A') ?? null,
            'created_at' => $this->created_at,
        ];
    }
}
