<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'title' => $this->title,
            'title_ar' => $this->title_ar,
            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'content_link' => $this->content_link,
            'lession_date' => $this->lession_date,
            'lession_time' => $this->lession_time,
            'link_video' => $this->link_video,
            'exams' => ExamResource::collection(
                $this->whenLoaded('exams')
            ),

            'assignments' => ExamResource::collection(
                $this->whenLoaded('assignments')
            ),
            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }
}
