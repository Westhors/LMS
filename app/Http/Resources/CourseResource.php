<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'stage_id' => $this->stage_id,
            'stage' => new StageResource($this->whenLoaded('stage')),
            'subject_id' => $this->subject_id,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'details' => CourseDetailResource::collection(
                $this->whenLoaded('details')
            ),
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'about' => $this->about,
            'about_ar' => $this->about_ar,
            'hour_time_course' => $this->hour_time_course,
            'type' => $this->type,
            'count_student' => $this->count_student,
            'price' => $this->price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'active' => $this->active ?? null,
            'link_video' => $this->link_video,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
