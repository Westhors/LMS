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
            'type_of_attendance' => $this->type_of_attendance,
            'gender' => $this->gender,
            'active' => $this->active,

            'teacher_id' => $this->teacher_id,
            'stage_id' => $this->stage_id,
            'stage' => $this->whenLoaded('stage'),

            'created_at' => $this->created_at,
        ];
    }
}
