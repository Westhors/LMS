<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'position' => $this->position ?? null,
            'active' => $this->active ?? null,
            'image' => $this->teacher_image ? [
                'id' => $this->teacher_image->id,
                'name' => $this->teacher_image->name,
                'mimeType' => $this->teacher_image->mime_type,
                'size' => $this->teacher_image->size,
                'authorId' => $this->teacher_image->author_id,
                'previewUrl' => '/storage/' . $this->teacher_image->file_path,
                'fullUrl' => asset('storage/' . $this->teacher_image->file_path),
                'createdAt' => optional($this->teacher_image->created_at)->format('d F, Y'),
            ] : null,
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}


