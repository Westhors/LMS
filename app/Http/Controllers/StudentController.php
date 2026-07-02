<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\StudentResource;
use App\Interfaces\StudentRepositoryInterface;
use App\Models\CourseDetailAttendance;
use App\Models\Student;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        // الحساب متوقف
        if ($student->device_blocked) {
            return response()->json([
                'status' => false,
                'message' => 'تم إيقاف الحساب بسبب محاولة تسجيل الدخول من جهاز آخر. برجاء التواصل مع الدعم أو المدرس لإعادة التفعيل.'
            ], 403);
        }

        // أول تسجيل أو بعد Reset
        if (
            empty($student->device_id) ||
            empty($student->fingerprint)
        ) {

            $student->update([
                'device_id' => $request->device_id,
                'fingerprint' => $request->fingerprint,
                'last_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        // جهاز مختلف
        elseif (
            $student->device_id !== $request->device_id ||
            $student->fingerprint !== $request->fingerprint
        ) {

            $student->update([
                'device_blocked' => true,
                'device_blocked_at' => now(),
            ]);

            $student->tokens()->delete();

            return response()->json([
                'status' => false,
                'message' => 'تم إيقاف الحساب لأنك حاولت تسجيل الدخول من جهاز آخر. برجاء التواصل مع الدعم أو المدرس لإعادة تفعيل الحساب.'
            ], 403);
        }
        // نفس الجهاز
        else {
            $student->update([
                'last_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // تسجيل دخول طبيعي
        $student->tokens()->delete();

        $token = $student->createToken(
            'token',
            [$request->type]
        )->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'type' => $request->type,
            'data' => new StudentResource($student),
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
        ]);

        $attendance = CourseDetailAttendance::with([
            'student',
            'courseDetail'
        ])
        ->where('course_detail_id', $request->course_detail_id)
        ->where('attended', 1) // لو عندك عمود attended بقيمة 1 للحضور
        ->get();

        if ($attendance->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No students attended this lesson'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'count' => $attendance->count(),
            'data' => $attendance
        ]);
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
}

