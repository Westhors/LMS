<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Models\LessonNote;
use Illuminate\Http\Request;

class LessonNoteController extends Controller
{
        public function storeOrUpdate(Request $request, $courseDetailId)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $studentId = auth('sanctum')->id() ?? auth()->id();

        $note = LessonNote::updateOrCreate(
            [
                'student_id' => $studentId,
                'course_detail_id' => $courseDetailId,
            ],
            [
                'note' => $request->note,
            ]
        );

        return JsonResponse::respondSuccess(
            'Note Saved Successfully',
            $note
        );
    }

    public function show($courseDetailId)
    {
        $studentId = auth('sanctum')->id() ?? auth()->id();

        $note = LessonNote::where('student_id', $studentId)
            ->where('course_detail_id', $courseDetailId)
            ->first();

        return JsonResponse::respondSuccess(
            'Note Fetched Successfully',
            $note
        );
    }
}
