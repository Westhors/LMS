<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentAttendanceResource;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoryInterface;
use App\Models\CourseDetail;
use App\Models\CourseDetailAttendance;
use App\Models\Enrollment;
use App\Models\Student;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(StudentRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Students = StudentResource::collection($this->crudRepository->all(['teacher', 'stage'], [], ['*']));
            return $Students->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(StudentRequest $request)
    {
        try {
            $student = $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function applicationForm(StudentRequest $request)
    {
        $student = Student::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
        ]);

        $student->code_parent = rand(1000, 9999) . $student->id;
        $student->barcode = rand(1000, 9999999);
        $student->save();

        if ($request->image) {
            $this->crudRepository->AddMediaCollection('image', $student);
        }

        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Student registered successfully',
            'token' => $token,
            'data' => new StudentResource($student),
        ]);
    }


    public function login(Request $request)
    {
        $request->validate([
            'type' => 'required|in:student,parent',
            'phone' => 'nullable',
            'password' => 'required',
            'device_id' => 'required|string',
            'fingerprint' => 'required|string',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Find Student
    |--------------------------------------------------------------------------
    */

        if ($request->type === 'student') {

            $student = Student::where('phone', $request->phone)
                ->where('active', true)
                ->first();

            if (!$student || !Hash::check($request->password, $student->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials or inactive account'
                ], 401);
            }
        } else {

            $student = Student::where('code_parent', $request->password)
                ->where('active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials or inactive account'
                ], 401);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Update Device Information
    |--------------------------------------------------------------------------
    |
    | بدون أي حظر أو تحقق من الجهاز
    |
    */

        $student->update([
            'device_id' => $request->device_id,
            'fingerprint' => $request->fingerprint,
            'last_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

        // حذف التوكن القديم حتى يكون هناك جلسة واحدة فعالة
        $student->tokens()->delete();

        $token = $student->createToken(
            'token',
            [$request->type]
        )->plainTextToken;

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'type' => $request->type,
            'data' => new StudentResource($student),
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|integer|exists:students,id',
        ]);

        $query = Student::whereIn('id', $request->items);

        if (auth()->check()) {
            $query->where('teacher_id', auth()->id());
        }

        $deleted = $query->delete();

        return response()->json([
            'status' => true,
            'message' => 'students deleted successfully',
            'deleted_count' => $deleted,
        ]);
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => new StudentResource($request->user())
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }


    public function show(Student $student): ?\Illuminate\Http\JsonResponse
    {
        try {
            $student->load(['teacher', 'stage']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new StudentResource($student));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(StudentRequest $request, Student $student): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $student->id);
            activity()->performedOn($student)->withProperties(['attributes' => $student])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('students', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Student::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Student::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Student::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function markAttendance($lessonId, $studentId)
    {
        try {

            CourseDetailAttendance::updateOrCreate(

                [
                    'course_detail_id' => $lessonId,
                    'student_id' => $studentId,
                ],

                [
                    'attended' => true,
                    'attended_at' => now(),
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Attendance marked successfully'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function showAttendance(Request $request)
    {
        $request->validate([
            'course_detail_id' => 'required|exists:course_details,id',
            'student_id' => 'required|exists:students,id',
        ]);

        $attendance = CourseDetailAttendance::with([
            'student',
            'courseDetail'
        ])
            ->where('course_detail_id', $request->course_detail_id)
            ->where('student_id', $request->student_id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $attendance->id,
                'attended' => $attendance->attended,
                'attended_at' => $attendance->attended_at,
                'student' => $attendance->student,
                'course_detail' => $attendance->courseDetail,
                'created_at' => $attendance->created_at,
                'updated_at' => $attendance->updated_at,
            ]
        ]);
    }

    public function allAttendance(Request $request)
    {
        $request->validate([
            'course_detail_id' => 'required|exists:course_details,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        try {

            /*
        |--------------------------------------------------------------------------
        | تسجيل حضور طالب
        |--------------------------------------------------------------------------
        */

            if ($request->filled('student_id')) {

                $courseDetailId = $request->course_detail_id;
                $studentId = $request->student_id;

                /*
            |--------------------------------------------------------------------------
            | 1. تأكد أن الطالب مشترك في الدرس
            |--------------------------------------------------------------------------
            */

                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id' => $studentId,
                        'type' => 'lesson',
                        'course_detail_id' => $courseDetailId,
                    ],
                    [
                        'course_id' => null,
                        'semester_id' => null,
                        'book_id' => null,
                    ]
                );

                /*
            |--------------------------------------------------------------------------
            | 2. سجل الحضور إذا لم يكن مسجلًا
            |--------------------------------------------------------------------------
            */

                $attendance = CourseDetailAttendance::firstOrCreate(
                    [
                        'course_detail_id' => $courseDetailId,
                        'student_id' => $studentId,
                    ]
                );

                $attendance->load([
                    'student',
                    'courseDetail'
                ]);

                return response()->json([
                    'success' => true,
                    'count' => 1,
                    'message' => 'Attendance recorded successfully',
                    'enrolled' => true,
                    'data' => $attendance
                ], 201);
            }


            /*
        |--------------------------------------------------------------------------
        | جلب كل حضور الدرس
        |--------------------------------------------------------------------------
        */

            $attendance = CourseDetailAttendance::with([
                'student',
                'courseDetail'
            ])
                ->where('course_detail_id', $request->course_detail_id)
                ->get();

            if ($attendance->isEmpty()) {

                return response()->json([
                    'success' => true,
                    'count' => 0,
                    'message' => 'No students found for this lesson',
                    'data' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'count' => $attendance->count(),
                'data' => $attendance
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resetStudentDevice(Student $student)
    {
        $student->update([
            'device_id' => null,
            'fingerprint' => null,
            'last_ip' => null,
            'user_agent' => null,
            'device_blocked' => false,
            'device_blocked_at' => null,
        ]);

        $student->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Student device has been reset successfully.'
        ]);
    }



    public function attendanceIndex(Request $request)
    {
        $filters = $request->input('filters', []);

        $attendances = CourseDetailAttendance::query()
            ->with([
                'student.stage',
                'student.centerHour',
            ])

            ->when(
                !empty($filters['course_detail_id']),
                fn($query) =>
                $query->where('course_detail_id', $filters['course_detail_id'])
            )

            ->whereHas('student', function ($query) use ($filters) {

                $query
                    ->when(
                        !empty($filters['name']),
                        fn($q) =>
                        $q->where('name', 'like', '%' . $filters['name'] . '%')
                    )

                    ->when(
                        !empty($filters['phone']),
                        fn($q) =>
                        $q->where('phone', 'like', '%' . $filters['phone'] . '%')
                    )

                    ->when(
                        !empty($filters['stage_id']),
                        fn($q) =>
                        $q->where('stage_id', $filters['stage_id'])
                    )

                    ->when(
                        !empty($filters['center_hour_id']),
                        fn($q) =>
                        $q->where('center_hour_id', $filters['center_hour_id'])
                    )

                    ->when(
                        !empty($filters['type_of_attendance']),
                        fn($q) =>
                        $q->where(
                            'type_of_attendance',
                            $filters['type_of_attendance']
                        )
                    )

                    ->when(
                        !empty($filters['barcode']),
                        fn($q) =>
                        $q->where(
                            'barcode',
                            'like',
                            '%' . $filters['barcode'] . '%'
                        )
                    )

                    ->when(
                        !empty($filters['teacher_id']),
                        fn($q) =>
                        $q->where('teacher_id', $filters['teacher_id'])
                    );
            })

            ->latest('id')
            ->get();

        return $attendances->map(function ($attendance) {

            $student = $attendance->student;

            if (!$student) {
                return null;
            }

            $student->setRelation('attendance', $attendance);

            return new StudentAttendanceResource($student);
        })->filter()->values();
    }




    public function removeAttendance($courseDetail, $student)
    {
        try {

            $attendance = CourseDetailAttendance::where(
                'course_detail_id',
                $courseDetail
            )
                ->where(
                    'student_id',
                    $student
                )
                ->first();

            if (!$attendance) {
                return JsonResponse::respondError(
                    'Attendance record not found'
                );
            }

            $attendance->delete();

            return JsonResponse::respondSuccess(
                'Student attendance removed successfully'
            );
        } catch (\Exception $e) {

            return JsonResponse::respondError(
                $e->getMessage()
            );
        }
    }


    public function studentCourseDelete($course_id, $student_id)
    {
        try {
            $enrollment = Enrollment::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->where('type', 'course')
                ->first();
            if (!$enrollment) {

                return JsonResponse::respondError(
                    'Student is not enrolled in this course'
                );
            }

            $enrollment->delete();
            return JsonResponse::respondSuccess(
                'Student course enrollment deleted successfully'
            );
        } catch (\Exception $e) {

            return JsonResponse::respondError(
                $e->getMessage()
            );
        }
    }
}
