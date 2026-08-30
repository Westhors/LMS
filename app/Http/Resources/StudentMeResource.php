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
            'device_id' => $this->device_id,
            'fingerprint' => $this->fingerprint,
            'last_ip' => $this->last_ip,
            'user_agent' => $this->user_agent,
            'device_blocked' => $this->device_blocked,
            'device_blocked_at' => $this->device_blocked_at,




            'barcode' => $this->barcode,
            'region' => $this->region,
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
            'centerHour' => $this->centerHour,

            'created_at' => $this->created_at,

            'exams' => $this->formatByType('exam'),
            'assignments' => $this->formatByType('assignment'),
        ];
    }

    private function formatByType($type)
    {
        return $this->answers
            ->filter(function ($answer) use ($type) {
                return $answer->exam
                    && strtolower($answer->exam->type) === strtolower($type);
            })
            ->groupBy('exam_id')
            ->map(function ($answers) {

                $exam = $answers->first()->exam;

                if (!$exam) {
                    return null;
                }

                return [
                    'exam' => [
                        'id' => $exam->id,
                        'title' => $exam->title,
                        'course_detail_id' => $exam->course_detail_id,
                        'total_marks' => $exam->total_marks,
                        'type' => $exam->type,
                    ],

                    'student_mark' => $answers->sum('mark'),

                    'questions' => $exam->questions->map(function ($q) use ($answers) {

                        $studentAnswer = $answers
                            ->where('question_id', $q->id)
                            ->first();

                        $studentAnswerValue = null;

                        // Essay
                        if ($q->question_type === 'essay') {

                            $studentAnswerValue = $studentAnswer?->answer;
                        }

                        // True / False
                        elseif ($q->question_type === 'true_false') {

                            if ($studentAnswer) {
                                $value = strtolower(trim((string) $studentAnswer->answer));

                                if ($value === 'true' || $value === '1') {
                                    $studentAnswerValue = 'صح';
                                } elseif ($value === 'false' || $value === '0') {
                                    $studentAnswerValue = 'غلط';
                                } else {
                                    $studentAnswerValue = $studentAnswer->answer;
                                }
                            }
                        }

                        // Multiple Choice
                        elseif ($q->question_type === 'multiple_choice') {

                            if ($studentAnswer?->answer) {

                                $option = $q->options
                                    ->where('id', $studentAnswer->answer)
                                    ->first();

                                $studentAnswerValue = $option?->option_text;
                            }
                        }

                        return [
                            'id' => $studentAnswer?->id,
                            'question_id' => $q->id,
                            'question' => $q->question,
                            'mark' => $q->mark,
                            'question_type' => $q->question_type,
                            'correct_answer' => $q->correct_answer,

                            'student_answer' => $studentAnswerValue,

                            'is_correct' => $studentAnswer?->is_correct,
                            'mark_obtained' => $studentAnswer?->mark,
                        ];
                    })->values(),
                ];
            })
            ->filter()
            ->values();
    }
}
