<?php

namespace App\Http\Resources;

use App\Models\CourseDetailView;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $studentId = auth('sanctum')->id() ?? auth()->id();

        $viewsCount = 0;
        $remaining = null;

        if ($studentId) {
            $viewsCount = CourseDetailView::where(
                'course_detail_id',
                $this->id
            )
                ->where(
                    'student_id',
                    $studentId
                )
                ->count();

            $remaining = $this->available_watch_count === null
                ? null
                : max(
                    0,
                    (int)$this->available_watch_count - $viewsCount
                );
        }
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'titles' => $this->titles,
            'titles_ar' => $this->titles_ar,

            'available_watch_count' => $this->available_watch_count,
            'usedWatchCount' => $viewsCount,
            'remainingWatchCount' => $remaining,

            'link_video' => $this->must_pass_to_unlock
                ? $this->checkStudentPassedExam()
                ? $this->link_video
                : 'You must pass the exam first'
                : $this->link_video,

            'description' => $this->description,
            'description_ar' => $this->description_ar,
            'content_link' => $this->must_pass_to_unlock
                ? $this->checkStudentPassedExam()
                ? $this->content_link
                : 'You must pass the exam first'
                : $this->content_link,
            'lession_date' => $this->lession_date,
            'lession_time' => $this->lession_time,
            'price' => $this->price,
            'must_pass_to_unlock' => (bool) $this->must_pass_to_unlock,
            'must_solve_assignment_to_unlock' => (bool) $this->must_solve_assignment_to_unlock,
            'exams' => ExamResource::collection(
                $this->availableExamsForStudent()
            ),
            'need_support' => $this->needSupport(),
            'discount' => $this->discount ?? null,

            'assignments' => ExamResource::collection(
                $this->whenLoaded('assignments')
            ),

            'students' => StudentResource::collection(
                $this->whenLoaded('students')
            ),

            'attended' => (bool) (
                $this->attendances
                ->where('student_id', auth()->id())
                ->first()?->attended
            ),

            'imageUrl' => $this->getFirstMediaUrl(),
            'image' => new MediaResource($this->getFirstMedia()),

            'pdfUrl' => $this->getFirstMediaUrl('pdf'),
            'pdf' => new MediaResource($this->getFirstMedia('pdf')),

            'createdAt' => $this->created_at->format('d F, Y'),
        ];
    }

private function availableExamsForStudent()
{
    $student = auth()->user();

    if (!$student) {
        return collect();
    }

    $exams = $this->relationLoaded('exams')
        ? $this->exams->sortBy('id')->values()
        : $this->exams()->orderBy('id')->get();

    $availableExams = collect();

    foreach ($exams as $exam) {

        $answers = $exam->answers()
            ->where('student_id', $student->id)
            ->get();

        // لم يمتحن هذا الامتحان
        if ($answers->isEmpty()) {
            $availableExams->push($exam);
            break;
        }

        // الامتحان الذي دخله الطالب يظهر دائماً
        $availableExams->push($exam);

        // فى انتظار تصحيح سؤال مقالى
        $hasPendingEssay = $answers
            ->load('question')
            ->contains(function ($answer) {
                return optional($answer->question)->question_type === 'essay'
                    && $answer->is_auto_corrected == 0
                    && is_null($answer->is_correct)
                    && is_null($answer->mark);
            });

        if ($hasPendingEssay) {
            break;
        }

        $studentMark = $answers->sum('mark');

        // نجح ➜ لا تُظهر الامتحانات التالية
        if ($studentMark >= $exam->total_must_pass_marks) {
            break;
        }

        // سقط ➜ سيكمل تلقائياً للامتحان التالي
    }

    return $availableExams;
}

private function needSupport()
{
    $student = auth()->user();

    if (!$student) {
        return false;
    }

    $exams = $this->relationLoaded('exams')
        ? $this->exams->sortBy('id')->values()
        : $this->exams()->orderBy('id')->get();

    if ($exams->isEmpty()) {
        return false;
    }

    foreach ($exams as $exam) {

        $answers = $exam->answers()
            ->where('student_id', $student->id)
            ->get();

        // لسه فيه امتحانات لم يدخلها
        if ($answers->isEmpty()) {
            return false;
        }

        // سؤال مقالى لم يُصحح
        $hasPendingEssay = $answers
            ->load('question')
            ->contains(function ($answer) {
                return optional($answer->question)->question_type === 'essay'
                    && $answer->is_auto_corrected == 0
                    && is_null($answer->is_correct)
                    && is_null($answer->mark);
            });

        if ($hasPendingEssay) {
            return false;
        }

        $studentMark = $answers->sum('mark');

        // نجح فى أى امتحان
        if ($studentMark >= $exam->total_must_pass_marks) {
            return false;
        }
    }

    // سقط فى جميع الامتحانات
    return true;
}
}






















// //
// <?php

// namespace App\Http\Resources;

// use App\Models\CourseDetailView;
// use Illuminate\Http\Resources\Json\JsonResource;

// class CourseDetailResource extends JsonResource
// {
//     public function toArray($request)
//     {
//         $studentId = auth('sanctum')->id() ?? auth()->id();

//         $viewsCount = 0;
//         $remaining = null;

//         if ($studentId) {
//             $viewsCount = CourseDetailView::where(
//                 'course_detail_id',
//                 $this->id
//             )
//                 ->where(
//                     'student_id',
//                     $studentId
//                 )
//                 ->count();

//             $remaining = $this->available_watch_count === null
//                 ? null
//                 : max(
//                     0,
//                     (int)$this->available_watch_count - $viewsCount
//                 );
//         }
//         return [
//             'id' => $this->id,
//             'course_id' => $this->course_id,
//             'course' => new CourseResource($this->whenLoaded('course')),
//             'titles' => $this->titles,
//             'titles_ar' => $this->titles_ar,

//             'available_watch_count' => $this->available_watch_count,
//             'usedWatchCount' => $viewsCount,
//             'remainingWatchCount' => $remaining,

//             'link_video' => $this->must_pass_to_unlock
//                 ? $this->checkStudentPassedExam()
//                 ? $this->link_video
//                 : 'You must pass the exam first'
//                 : $this->link_video,

//             'description' => $this->description,
//             'description_ar' => $this->description_ar,
//             'content_link' => $this->must_pass_to_unlock
//                 ? $this->checkStudentPassedExam()
//                 ? $this->content_link
//                 : 'You must pass the exam first'
//                 : $this->content_link,
//             'lession_date' => $this->lession_date,
//             'lession_time' => $this->lession_time,
//             'price' => $this->price,
//             'must_pass_to_unlock' => (bool) $this->must_pass_to_unlock,
//             'must_solve_assignment_to_unlock' => (bool) $this->must_solve_assignment_to_unlock,
//             'exams' => ExamResource::collection(
//                 $this->availableExamsForStudent()
//             ),
//             'need_support' => $this->needSupport(),
//             'discount' => $this->discount ?? null,

//             'assignments' => ExamResource::collection(
//                 $this->whenLoaded('assignments')
//             ),

//             'students' => StudentResource::collection(
//                 $this->whenLoaded('students')
//             ),

//             'attended' => (bool) (
//                 $this->attendances
//                 ->where('student_id', auth()->id())
//                 ->first()?->attended
//             ),

//             'imageUrl' => $this->getFirstMediaUrl(),
//             'image' => new MediaResource($this->getFirstMedia()),

//             'pdfUrl' => $this->getFirstMediaUrl('pdf'),
//             'pdf' => new MediaResource($this->getFirstMedia('pdf')),

//             'createdAt' => $this->created_at->format('d F, Y'),
//         ];
//     }

// private function availableExamsForStudent()
// {
//     $student = auth()->user();

//     if (!$student) {
//         return collect();
//     }

//     $exams = $this->relationLoaded('exams')
//         ? $this->exams->sortBy('id')->values()
//         : $this->exams()->orderBy('id')->get();

//     $availableExams = collect();

//     foreach ($exams as $exam) {

//         $answers = $exam->answers()
//             ->where('student_id', $student->id)
//             ->get();

//         // لم يمتحن هذا الامتحان
//         if ($answers->isEmpty()) {
//             $availableExams->push($exam);
//             break;
//         }

//         // الامتحان الذي دخله الطالب يظهر دائماً
//         $availableExams->push($exam);

//         // فى انتظار تصحيح سؤال مقالى
//         $hasPendingEssay = $answers
//             ->load('question')
//             ->contains(function ($answer) {
//                 return optional($answer->question)->question_type === 'essay'
//                     && $answer->is_auto_corrected == 0
//                     && is_null($answer->is_correct)
//                     && is_null($answer->mark);
//             });

//         if ($hasPendingEssay) {
//             break;
//         }

//         $studentMark = $answers->sum('mark');

//         // نجح ➜ لا تُظهر الامتحانات التالية
//         if ($studentMark >= $exam->total_must_pass_marks) {
//             break;
//         }

//         // سقط ➜ سيكمل تلقائياً للامتحان التالي
//     }

//     return $availableExams;
// }

// private function needSupport()
// {
//     $student = auth()->user();

//     if (!$student) {
//         return false;
//     }

//     $exams = $this->relationLoaded('exams')
//         ? $this->exams->sortBy('id')->values()
//         : $this->exams()->orderBy('id')->get();

//     if ($exams->isEmpty()) {
//         return false;
//     }

//     foreach ($exams as $exam) {

//         $answers = $exam->answers()
//             ->where('student_id', $student->id)
//             ->get();

//         // لسه فيه امتحانات لم يدخلها
//         if ($answers->isEmpty()) {
//             return false;
//         }

//         // سؤال مقالى لم يُصحح
//         $hasPendingEssay = $answers
//             ->load('question')
//             ->contains(function ($answer) {
//                 return optional($answer->question)->question_type === 'essay'
//                     && $answer->is_auto_corrected == 0
//                     && is_null($answer->is_correct)
//                     && is_null($answer->mark);
//             });

//         if ($hasPendingEssay) {
//             return false;
//         }

//         $studentMark = $answers->sum('mark');

//         // نجح فى أى امتحان
//         if ($studentMark >= $exam->total_must_pass_marks) {
//             return false;
//         }
//     }

//     // سقط فى جميع الامتحانات
//     return true;
// }
// }
// //
