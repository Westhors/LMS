<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploadAction;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamAnswerController extends Controller
{
    public function passStudent(Request $request)
    {
        $result = (new FileUploadAction())->checkAssistantPermission('correct-answers', 'create');
            if ($result !== true) {
                return $result;
        }
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
        ]);

        DB::beginTransaction();

        try {

            $answer = ExamAnswer::where('exam_id', $request->exam_id)
                ->where('student_id', $request->student_id)
                ->inRandomOrder()
                ->first();

            if (!$answer) {
                return response()->json([
                    'status' => false,
                    'message' => 'No answers found for this student'
                ], 404);
            }

            $answer->update([
                'mark' => 100,
                'is_correct' => 1,
                'is_auto_corrected' => 0,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student passed successfully',
                'answer_id' => $answer->id,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
