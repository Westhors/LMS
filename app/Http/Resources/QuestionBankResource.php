<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionBankResource extends JsonResource
{
    public function toArray($request)
    {
        $image = $this->question_image->first();
        return [
            'id' => $this->id,
            'teacher' => $this->teacher->name,
            'stage' => $this->stage->name,
            'subject' => $this->subject->name,
            'course_detail' => $this->courseDetail ? $this->courseDetail->name : null,
            'question_type' => $this->question_type,
            'question' => $this->question,
            'mark' => $this->mark,
            'correct_answer' => $this->correct_answer,
            'image' => $image ? [

                'id' => $image->id,

                'name' => $image->name,

                'mimeType' => $image->mime_type,

                'size' => $image->size,

                'previewUrl' => '/storage/' . $image->file_path,

                'fullUrl' => asset('storage/' . $image->file_path),

            ] : null,
            'options' => $this->options->map(function ($option) {
                return [

                    'id' => $option->id,

                    'option_text' => $option->option_text,

                    'is_correct' => (bool) $option->is_correct,
                ];
            }),
            'createdAt' => optional($this->created_at)
                ->format('d F, Y'),
        ];
    }
}
