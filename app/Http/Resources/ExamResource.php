<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
         $studentId = auth('sanctum')->id() ?? auth()->id();

        $studentSolved = false;
        $studentMark = 0;
        $studentPassed = false;
        $studentPassedMessage = null;

        if ($studentId) {

            $studentAnswers = $this->answers()
                ->where('student_id', $studentId)
                ->get();

            $studentSolved = $studentAnswers->isNotEmpty();
            $studentMark = $studentAnswers->sum('mark');

            $hasPendingEssay = $studentAnswers
                ->load('question')
                ->contains(function ($answer) {
                    return optional($answer->question)->question_type === 'essay'
                        && $answer->is_auto_corrected == 0
                        && is_null($answer->is_correct)
                        && is_null($answer->mark);
                });

            if ($hasPendingEssay) {
                $studentPassed = null;
                $studentPassedMessage = 'جارى تصحيح الامتحان انتظر !';
            } else {
                $studentPassed = $studentMark >= $this->total_must_pass_marks;
            }
        }

        return [
            'id' => $this->id,

            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'course_detail_id' => new CourseDetailResource($this->whenLoaded('courseDetail')),
            'stage_id' => new StageResource($this->whenLoaded('stage')),
            'teacher_id' => new TeacherResource($this->whenLoaded('teacher')),
            'student_solved' => $studentSolved,
            'student_mark' => $studentMark,
            'student_passed' => $studentPassed,
            'student_passed_message' => $studentPassedMessage,

            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'students' => $this->whenLoaded('answers', function () {
                return $this->answers
                    ->load('student')
                    ->groupBy('student_id')
                    ->map(function ($answers) {

                        $student = $answers->first()->student;

                        return [
                            'id' => $student->id,
                            'name' => $student->name,

                            'answers' => $answers->map(function ($answer) {

                                $image = DB::table('mediable')
                                    ->join('media', 'media.id', '=', 'mediable.media_id')
                                    ->where('mediable.model_type', \App\Models\ExamAnswer::class)
                                    ->where('mediable.model_id', $answer->id)
                                    ->where('mediable.collection', 'answer_image')
                                    ->first();

                                return [
                                    'id' => $answer->id,
                                    'question_id' => $answer->question_id,
                                    'answer' => $answer->answer,
                                    'mark' => $answer->mark,
                                    'is_auto_corrected' => $answer->is_auto_corrected,
                                    'is_correct' => $answer->is_correct,

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

                                    'created_at' => $answer->created_at?->format('Y-m-d H:i:s'),
                                ];
                            })->values(),
                        ];
                    })->values();
            }),
            'total_marks' => $this->total_marks,
            'total_must_pass_marks' => $this->total_must_pass_marks,
            'duration_minutes' => $this->duration_minutes,
            'active' => $this->active,

                'time_start' => $this->time_start,
                'time_end' => $this->time_end,
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
