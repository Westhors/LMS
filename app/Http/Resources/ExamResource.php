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
                                return [
                                    'id' => $answer->id,
                                    'question_id' => $answer->question_id,
                                    'answer' => $answer->answer,
                                    'mark' => $answer->mark,
                                    'is_auto_corrected' => $answer->is_auto_corrected,
                                    'is_correct' => $answer->is_correct,
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
