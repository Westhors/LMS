<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'position' => $this->position ?? null,
            'active' => $this->active ?? null,
            'stage' => new StageResource($this->stage),
            'semesters' => SemesterResource::collection($this->whenLoaded('semesters')),
            'stage_id' => $this->stage_id,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}

