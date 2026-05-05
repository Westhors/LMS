<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
use App\Models\PaymentCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function requestEnroll(Request $request)
    {
        $request->validate([
            'type' => 'required|in:course,semester,lesson',
            'course_id' => 'nullable',
            'semester_id' => 'nullable',
            'course_detail_id' => 'nullable',
            'price' => 'required|numeric'
        ]);

        $student = auth()->user();

        DB::beginTransaction();

        try {

            // 💰 لو عنده رصيد → شراء مباشر
            if ($student->balance >= $request->price) {

                $student->decrement('balance', $request->price);

                Enrollment::create([
                    'student_id' => $student->id,
                    'type' => $request->type,
                    'course_id' => $request->course_id,
                    'semester_id' => $request->semester_id,
                    'course_detail_id' => $request->course_detail_id,
                ]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Purchased successfully from wallet'
                ]);
            }

            // ❌ مفيش رصيد → Request للمدرس
            EnrollmentRequest::create([
                'student_id' => $student->id,
                'teacher_id' => $student->teacher_id,
                'type' => $request->type,
                'course_id' => $request->course_id,
                'semester_id' => $request->semester_id,
                'course_detail_id' => $request->course_detail_id,
                'price' => $request->price,
                'status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Request sent to teacher'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function teacherRequests()
    {
       return EnrollmentRequest::where('teacher_id', auth()->id())
        ->with(['student:id,name,phone'])
        ->latest()
        ->get();
    }
    
    public function reject($id)
    {
        $request = EnrollmentRequest::findOrFail($id);

        $request->update([
           'status' => 'rejected'
        ]);

         return response()->json([
            'status' => true,
             'message' => 'Rejected'
        ]);
    }

    public function redeemCode(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $code = PaymentCode::where('code', $request->code)
            ->where('is_used', false)
            ->first();

        if (!$code) {
            return response()->json(['message' => 'Invalid or used code'], 400);
        }

        DB::beginTransaction();

        try {

            // 🎟️ mark code as used
            $code->update([
                'is_used' => true,
                'student_id' => auth()->id(),
                'used_at' => now()
            ]);

            // 🎓 create enrollment
            $enrollment = Enrollment::create([
                'student_id' => auth()->id(),
                'type' => $code->type,
                'course_id' => $code->course_id,
                'semester_id' => $code->semester_id,
                'course_detail_id' => $code->course_detail_id,
            ]);

            // 🔥 update request (لو موجود)
            $requestRow = EnrollmentRequest::
                 where('student_id', auth()->id())
                ->where('type', $code->type)
                ->where('course_id', $code->course_id)
                ->where('semester_id', $code->semester_id)
                ->where('course_detail_id', $code->course_detail_id)
                ->where('status', 'pending')
                ->first();

            if ($requestRow) {
                $requestRow->update([
                    'status' => 'approved'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Enrolled successfully & request approved'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function redeemWallet(Request $request)
    {
        $code = PaymentCode::where('code', $request->code)
            ->where('type', 'wallet')
            ->where('is_used', false)
            ->first();

        if (!$code) {
            return response()->json(['message' => 'Invalid code']);
        }

        $student = auth()->user();

        $student->increment('balance', $code->amount);

        $code->update([
            'is_used' => true,
            'student_id' => $student->id,
            'used_at' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Wallet updated'
        ]);
    }
}
