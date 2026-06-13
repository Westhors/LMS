<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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

            $image = DB::table('mediable')
                ->join('media', 'media.id', '=', 'mediable.media_id')
                ->where('mediable.model_type', \App\Models\QuestionOption::class)
                ->where('mediable.model_id', $option->id)
                ->where('mediable.collection', 'option_image')
                ->first();

            return [
                'id' => $option->id,
                'option_text' => $option->option_text,
                'is_correct' => (bool) $option->is_correct,

                // 👇 إضافة الصورة
                'image' => $image ? [
                    'id' => $image->id,
                    'name' => $image->name,
                    'mimeType' => $image->mime_type,
                    'size' => $image->size,
                    'authorId' => $image->author_id,
                    'previewUrl' => '/storage/' . $image->file_path,
                    'fullUrl' => asset('storage/' . $image->file_path),
                    'createdAt' => optional($image->created_at)?->format('d F, Y'),
                ] : null,
            ];
        });
    }
),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
