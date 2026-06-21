<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\SemesterResource;
use App\Http\Resources\StudentMeResource;
use App\Http\Resources\StudentResource;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseDetail;
use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
use App\Models\PaymentCode;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
public function requestEnroll(Request $request)
{
    $request->validate([
        'type' => 'required|in:course,semester,lesson,book',
        'course_id' => 'nullable',
        'semester_id' => 'nullable',
        'course_detail_id' => 'nullable',
        'book_id' => 'nullable',
        'price' => 'required|numeric'
    ]);

    $student = auth()->user();

    // منع الاشتراك مرتين
    $alreadyEnrolled = Enrollment::where('student_id', $student->id)
        ->where('type', $request->type)
        ->where('course_id', $request->course_id)
        ->where('semester_id', $request->semester_id)
        ->where('course_detail_id', $request->course_detail_id)
        ->where('book_id', $request->book_id)
        ->exists();

    if ($alreadyEnrolled) {
        return response()->json([
            'status' => false,
            'message' => 'You are already enrolled'
        ], 400);
    }

    DB::beginTransaction();

    try {

        // 💰 لو عنده رصيد كافي → شراء مباشر
        if ($student->balance >= $request->price) {

            $student->decrement('balance', $request->price);

            Enrollment::create([
                'student_id' => $student->id,
                'type' => $request->type,
                'course_id' => $request->course_id,
                'semester_id' => $request->semester_id,
                'course_detail_id' => $request->course_detail_id,
                'book_id' => $request->book_id,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchased successfully from wallet'
            ]);
        }

        // ❌ الرصيد غير كافي → إرسال طلب للمدرس
        EnrollmentRequest::create([
            'student_id' => $student->id,
            'teacher_id' => $student->teacher_id,
            'type' => $request->type,
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
            'course_detail_id' => $request->course_detail_id,
            'book_id' => $request->book_id,
            'price' => $request->price,
            'status' => 'pending',
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'رصيد المحفظة غير كاف، وتم إرسال طلب للمدرس'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function teacherRequests()
    {
        return EnrollmentRequest::where('teacher_id', auth()->id())
            ->where('status', 'pending')
            ->with(['student:id,name,phone'])
            ->latest()
            ->get();
    }

    public function status($id)
    {
        $request = EnrollmentRequest::findOrFail($id);

        $request->update([
           'status' => $request->status
        ]);

         return response()->json([
            'status' => true,
             'message' => 'Status updated'
        ]);
    }


    public function redeemCode(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $student = auth()->user();

        $code = PaymentCode::where('code', $request->code)
            ->where('is_used', false)
            ->first();

        if (!$code) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or already used code'
            ], 400);
        }

        DB::beginTransaction();

        try {

            // 🔥 لو الكود Wallet
            if ($code->type === 'wallet') {

                $student->increment('balance', $code->amount);

                $code->update([
                    'is_used' => true,
                    'student_id' => $student->id,
                    'used_at' => now()
                ]);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'تم شحن المحفظة بنجاح'
                ]);
            }

            // 🔥 حماية إضافية (لو نوع غريب)
            if (!in_array($code->type, ['course', 'semester', 'lesson'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'هذا الكود غير مخصص للاشتراك'
                ], 400);
            }

            // منع الاشتراك مرتين
            $alreadyEnrolled = Enrollment::where('student_id', $student->id)
                ->where('type', $code->type)
                ->where('course_id', $code->course_id)
                ->where('semester_id', $code->semester_id)
                ->where('course_detail_id', $code->course_detail_id)
                ->where('book_id', $code->book_id)
                ->exists();

            if ($alreadyEnrolled) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are already enrolled'
                ], 400);
            }

            // 🎟️ mark code as used
            $code->update([
                'is_used' => true,
                'student_id' => $student->id,
                'used_at' => now()
            ]);

            // 🎓 create enrollment
            Enrollment::create([
                'student_id' => $student->id,
                'type' => $code->type,
                'course_id' => $code->course_id,
                'semester_id' => $code->semester_id,
                'course_detail_id' => $code->course_detail_id,
                'book_id' => $code->book_id,
            ]);

            // 🔥 update request
            $requestRow = EnrollmentRequest::where('student_id', $student->id)
                ->where('type', $code->type)
                ->where('course_id', $code->course_id)
                ->where('semester_id', $code->semester_id)
                ->where('course_detail_id', $code->course_detail_id)
                ->where('book_id', $code->book_id)
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
                'message' => 'تم الاشتراك بنجاح'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ، حاول مرة أخرى'
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

    public function studentLearning($id)
    {
        $student = Student::find($id);

        $enrollments = Enrollment::where('student_id', $student->id)->get();
        // 🎓 IDs
        $semesterIds = $enrollments->where('type', 'semester')->pluck('semester_id')->unique()->values();
        $courseIds   = $enrollments->where('type', 'course')->pluck('course_id')->unique()->values();
        $lessonIds   = $enrollments->where('type', 'lesson')->pluck('course_detail_id')->unique()->values();

        // 🎓 Semesters
        $semesters = Semester::with([
            'courses.details',
            'courses.teacher',
            'courses.subject',
            'courses.stage',
            'courses.media',
            'courses.details.media'
        ])->whereIn('id', $semesterIds)->get();

        // 📚 Courses
        $courses = Course::with([
            'details',
            'teacher',
            'subject',
            'stage',
            'media',
            'details.media',
            'semester'
        ])->whereIn('id', $courseIds)->get();

        // 📖 Lessons
        $lessons = CourseDetail::with([
            'course.teacher',
            'course.semester',
            'media'
        ])->whereIn('id', $lessonIds)->get();

        return response()->json([
            'status' => true,
            'data' => [
                // 👤 Student من الريسورس
                'student' => new StudentMeResource($student),

                // 🎓 Learning Data
                'semesters' => SemesterResource::collection($semesters),
                'courses'   => CourseResource::collection($courses),
                'lessons'   => CourseDetailResource::collection($lessons),
            ]
        ]);
    }

    public function barcodeStudentLearning($barcode)
    {
        $student = Student::where('barcode', $barcode)->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $enrollments = Enrollment::where('student_id', $student->id)->get();

        // 🎓 IDs
        $semesterIds = $enrollments->where('type', 'semester')->pluck('semester_id')->unique()->values();
        $courseIds   = $enrollments->where('type', 'course')->pluck('course_id')->unique()->values();
        $lessonIds   = $enrollments->where('type', 'lesson')->pluck('course_detail_id')->unique()->values();

        // 🎓 Semesters
        $semesters = Semester::with([
            'courses.details',
            'courses.teacher',
            'courses.subject',
            'courses.stage',
            'courses.media',
            'courses.details.media'
        ])->whereIn('id', $semesterIds)->get();

        // 📚 Courses
        $courses = Course::with([
            'details',
            'teacher',
            'subject',
            'stage',
            'media',
            'details.media',
            'semester'
        ])->whereIn('id', $courseIds)->get();

        // 📖 Lessons
        $lessons = CourseDetail::with([
            'course.teacher',
            'course.semester',
            'media'
        ])->whereIn('id', $lessonIds)->get();

        return response()->json([
            'status' => true,
            'data' => [
                'student' => new StudentMeResource($student),
                'semesters' => SemesterResource::collection($semesters),
                'courses' => CourseResource::collection($courses),
                'lessons' => CourseDetailResource::collection($lessons),
            ]
        ]);
    }
    public function myLearning()
    {
        $student = auth()->user();

        $enrollments = Enrollment::where('student_id', $student->id)->get();

        // 🎓 IDs
        $semesterIds = $enrollments->where('type', 'semester')->pluck('semester_id')->unique()->values();
        $courseIds   = $enrollments->where('type', 'course')->pluck('course_id')->unique()->values();
        $lessonIds   = $enrollments->where('type', 'lesson')->pluck('course_detail_id')->unique()->values();
        $bookIds = $enrollments->where('type', 'book')->pluck('book_id')->unique()->values();

        // 🎓 Semesters
        $semesters = Semester::with([
            'courses.details',
            'courses.teacher',
            'courses.subject',
            'courses.stage',
            'courses.media',
            'courses.details.media'
        ])->whereIn('id', $semesterIds)->get();

        // 📚 Courses
        $courses = Course::with([
            'details',
            'teacher',
            'subject',
            'stage',
            'media',
            'details.media',
            'semester'
        ])->whereIn('id', $courseIds)->get();

        // 📖 Lessons
        $lessons = CourseDetail::with([
            'course.teacher',
            'course.semester',
            'media'
        ])->whereIn('id', $lessonIds)->get();

        $books = Book::with([
            'media'
        ])->whereIn('id', $bookIds)->get();
        return response()->json([
            'status' => true,
            'data' => [
                // 👤 Student من الريسورس
                'student' => new StudentMeResource($student),

                // 🎓 Learning Data
                'semesters' => SemesterResource::collection($semesters),
                'courses'   => CourseResource::collection($courses),
                'lessons'   => CourseDetailResource::collection($lessons),
                'books'     => BookResource::collection($books),

            ]
        ]);
    }
}
