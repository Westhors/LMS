<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentMeResource extends JsonResource
{
    public function toArray($request)
    {
        $this->loadMissing([
            'answers.exam.questions.options'
        ]);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_parent' => $this->phone_parent,
            'code_parent' => $this->code_parent,
            'type_of_attendance' => $this->type_of_attendance,
            'gender' => $this->gender,
            'governorate' => $this->governorate,
            'school_name' => $this->school_name,
            'type_of_study' => $this->type_of_study,
            'active' => $this->active,
            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),
            'teacher_id' => $this->teacher_id,
            'stage_id' => $this->stage_id,
            'stage' => $this->whenLoaded('stage'),

            'created_at' => $this->created_at,

            'exams' => $this->formatByType('exam'),
            'assignments' => $this->formatByType('assignment'),
        ];
    }

    private function formatByType($type)
    {
        return $this->answers
            ->where('exam.type', $type)
            ->groupBy('exam_id')
            ->map(function ($answers) {

                $exam = $answers->first()->exam;

                return [
                    'exam' => [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'total_marks' => $exam->total_marks,
                        'type' => $exam->type,
                    ],

                    // 🧮 total mark
                    'student_mark' => $answers->sum('mark'),

                    // 📖 questions
                    'questions' => $exam->questions->map(function ($q) use ($answers) {

                        $studentAnswer = $answers->where('question_id', $q->id)->first();

                        return [
                            'id' => $studentAnswer?->id,
                            'question_id' => $q->id,
                            'question' => $q->question,
                            'mark' => $q->mark,
                            'question_type' => $q->question_type,
                            'correct_answer' => $q->correct_answer,

                            'student_answer' => $studentAnswer?->answer,
                            'is_correct' => $studentAnswer?->is_correct,
                            'mark_obtained' => $studentAnswer?->mark,
                        ];
                    }),
                ];
            })
            ->values();
    }
}
