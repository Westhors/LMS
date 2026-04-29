<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'exam_id' => $this->exam_id,

            'question_type' => $this->question_type,
            'question' => $this->question,
            'mark' => $this->mark,

            // يظهر فقط لو موجود
            'correct_answer' => $this->when(
                !is_null($this->correct_answer),
                $this->correct_answer
            ),

            'options' => $this->whenLoaded(
                'options',
                function () {
                    return $this->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                            'is_correct' => (bool) $option->is_correct,
                        ];
                    });
                }
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
