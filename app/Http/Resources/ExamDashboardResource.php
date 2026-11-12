<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamDashboardResource extends JsonResource
{
    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'course_detail_id' => $this->course_detail_id,
            'stage_id' => $this->stage_id,
            'teacher_id' => $this->teacher_id,
            'type_exam' => $this->type_exam,
            'random_questions' => (bool) $this->random_questions,
            'random_answers' => (bool) $this->random_answers,
            'show_result' => (bool) $this->show_result,
            // 'must_pass_to_unlock' => (bool) $this->must_pass_to_unlock,

            'active' => $this->active,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
