<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'exam_id' => $this->exam_id,
            'question_id' => $this->question_id,
            'student_id' => $this->student_id,

            'answer' => $this->answer,
            'mark' => $this->mark,

            'is_auto_corrected' => $this->is_auto_corrected,
            'is_correct' => $this->is_correct,

            'question' => new QuestionResource(
                $this->whenLoaded('question')
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
