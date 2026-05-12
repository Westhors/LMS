<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'exam_id' => $this->exam_id,
            'question_type' => $this->question_type,
            'question' => $this->question,
            'mark' => $this->mark,
            'image' => $this->question_image->first() ? [
                'id' => $this->question_image->first()->id,
                'name' => $this->question_image->first()->name,
                'mimeType' => $this->question_image->first()->mime_type,
                'size' => $this->question_image->first()->size,
                'authorId' => $this->question_image->first()->author_id,
                'previewUrl' => '/storage/' . $this->question_image->first()->file_path,
                'fullUrl' => asset('storage/' . $this->question_image->first()->file_path),
                'createdAt' => optional(
                    $this->question_image->first()->created_at
                )->format('d F, Y'),
            ] : null,
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
