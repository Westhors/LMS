<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'course_detail_id' => new CourseDetailResource($this->whenLoaded('courseDetail')),
            'stage_id' => new StageResource($this->whenLoaded('stage')),
            'teacher_id' => new TeacherResource($this->whenLoaded('teacher')),


            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'answers' => AnswerResource::collection($this->whenLoaded('answers')),
            'total_marks' => $this->total_marks,
            'duration_minutes' => $this->duration_minutes,

            'active' => $this->active,

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
