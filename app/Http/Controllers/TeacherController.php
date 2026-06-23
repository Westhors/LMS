<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\TeacherRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Http\Resources\TeacherResource;
use App\Interfaces\TeacherRepositoryInterface;
use App\Models\Book;
use App\Models\Course;
use App\Models\CourseDetail;
use App\Models\Enrollment;
use App\Models\EnrollmentRequest;
use App\Models\Exam;
use App\Models\PaymentCode;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDF;
use Carbon\Carbon;
class TeacherController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(TeacherRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $brands = TeacherResource::collection($this->crudRepository->all(
                ['stages.media', 'subjects'],[],['*']
            ));
            return $brands->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(TeacherRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $teacher = $this->crudRepository->create($data);
            if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $teacher);
            }
            if ($request->filled('stage')) {
                foreach ($request->stage as $item) {

                    $teacher->stages()->sync($item['stage_id']);

                    if (!empty($item['image'])) {
                        DB::table('mediable')->insert([
                            'model_type' => \App\Models\Stage::class,
                            'model_id'   => $item['stage_id'],
                            'media_id'   => $item['image'],
                            'collection' => 'stage_image',
                            'teacher_id' => $teacher->id
                        ]);
                    }
                }
            }
            if ($request->filled('subject')) {
                foreach ($request->subject as $item) {
                    $teacher->subjects()->sync($item['subject_id']);
                }
            }
            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_ADDED_SUCCESSFULLY)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(Teacher $teacher): \Illuminate\Http\JsonResponse
    {
        try {
            $teacher->load([
                'stages',
                'subjects',
                'teacherImage',
                'assistantTeachers',
                'home',
                'features',
                'about',
                'footer',

                // books
                'books',

                // courses
                'courses.details.exams.questions.options',
                'courses.details.assignments.questions.options',
            ]);

            $teacher->stages->each(function ($stage) use ($teacher) {
                $stage->teacher_image = \DB::table('mediable')
                    ->join('media', 'media.id', '=', 'mediable.media_id')
                    ->where('mediable.teacher_id', $teacher->id)
                    ->where('mediable.model_type', \App\Models\Stage::class)
                    ->where('mediable.model_id', $stage->id)
                    ->where('mediable.collection', 'stage_image')
                    ->first();
            });

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new TeacherResource($teacher)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function subDomain(Teacher $teacher): \Illuminate\Http\JsonResponse
    {
        try {
            if (!$teacher->active) {
                 return JsonResponse::respondError('Teacher is inactive');
            }
            $teacher->load([
                'stages',
                'subjects',
                'teacherImage',
                'assistantTeachers',
                'home',
                'features',
                'about',
                'footer',

                // books
                'books',
                'centerHours',

                // courses
                'courses.details.exams.questions.options',
                'courses.details.assignments.questions.options',
            ]);

            $teacher->stages->each(function ($stage) use ($teacher) {
                $stage->teacher_image = \DB::table('mediable')
                    ->join('media', 'media.id', '=', 'mediable.media_id')
                    ->where('mediable.teacher_id', $teacher->id)
                    ->where('mediable.model_type', \App\Models\Stage::class)
                    ->where('mediable.model_id', $stage->id)
                    ->where('mediable.collection', 'stage_image')
                    ->first();
            });

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new TeacherResource($teacher)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(TeacherUpdateRequest $request, Teacher $teacher)
    {
        try {

            $data = $request->validated();

            unset($data['stage'], $data['subject']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // تحديث البيانات
            $this->crudRepository->update($data, $teacher->id);

            // إعادة تحميل المدرس
            $teacher = Teacher::findOrFail($teacher->id);

            // الصورة
            if ($request->filled('image')) {
                $this->crudRepository->AddMediaCollection('image', $teacher);
            }

            // المراحل
            if ($request->filled('stage')) {

                $stageIds = collect($request->stage)
                    ->pluck('stage_id')
                    ->toArray();

                $teacher->stages()->sync($stageIds);

                foreach ($request->stage as $item) {

                    if (!empty($item['image'])) {

                        DB::table('mediable')
                            ->where('model_type', \App\Models\Stage::class)
                            ->where('model_id', $item['stage_id'])
                            ->where('collection', 'stage_image')
                            ->where('teacher_id', $teacher->id)
                            ->delete();

                        DB::table('mediable')->insert([
                            'model_type' => \App\Models\Stage::class,
                            'model_id'   => $item['stage_id'],
                            'media_id'   => $item['image'],
                            'teacher_id' => $teacher->id,
                            'collection' => 'stage_image',
                        ]);
                    }
                }
            }

            // المواد
            if ($request->filled('subject')) {

                $subjectIds = collect($request->subject)
                    ->pluck('subject_id')
                    ->toArray();

                $teacher->subjects()->sync($subjectIds);
            }

            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY)
            );

        } catch (Exception $e) {

            return JsonResponse::respondError($e->getMessage());

        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('teachers', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Teacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Teacher::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Teacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function fetchTeacher(Request $request)
    {
        try {
            $TeacherData = Teacher::get();
            return TeacherResource::collection($TeacherData)->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function register(TeacherRequest $request)
    {

    }



    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $email = $credentials['email'];

            $teacher = Teacher::where('active', 1)
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (!$teacher) {
                $teacher = Teacher::where('active', 1)
                    ->whereRaw('LOWER(secound_email) = ?', [$email])
                    ->first();
            }

            if (!$teacher || !Hash::check($credentials['password'], $teacher->password)) {
                return JsonResponse::respondError('Invalid email or password', 401);
            }

            $token = $teacher->createToken('teacher_token')->plainTextToken;

            return JsonResponse::respondSuccess([
                'message' => 'Login successful',
                'teacher' => new TeacherResource($teacher),
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function checkAuth()
    {
        try {
            $teacher = Auth::user();

            if (!$teacher) {
                return JsonResponse::respondError('Unauthenticated', 401);
            }

            return JsonResponse::respondSuccess([
                'message' => 'Authenticated',
                'teacher' => new TeacherResource($teacher->load(['stages', 'subjects'])),
            ]);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function activateTheme(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'theme' => 'required|in:theme1,theme2',
            'backgroud_color' => 'nullable|string',
            'font_color' => 'nullable|string',
        ]);

        $teacher = Teacher::findOrFail($data['teacher_id']);

        $teacher->update([
            'theme' => $data['theme'],
            'backgroud_color' => $data['backgroud_color'] ?? $teacher->backgroud_color,
            'font_color' => $data['font_color'] ?? $teacher->font_color,
        ]);

        $teacher->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Theme activated successfully',
            'data' => [
                'teacher_id' => $teacher->id,
                'active_theme' => $teacher->theme,
                'active_backgroud_color' => $teacher->backgroud_color,
                'active_font_color' => $teacher->font_color,
                'themes' => [
                    [
                        'name' => 'theme1',
                        'active' => $teacher->theme === 'theme1',
                    ],
                    [
                        'name' => 'theme2',
                        'active' => $teacher->theme === 'theme2',
                    ],
                ],
            ],
        ]);
    }

    public function getTeacherTheme(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);

        return response()->json([
            'status' => true,
            'teacher_id' => $teacher->id,
            'active_theme' => $teacher->theme ?? 'theme1',
            'active_backgroud_color' => $teacher->backgroud_color,
            'active_font_color' => $teacher->font_color,
        ]);
    }


    public function teacherReport($teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);

        // الكورسات
        $onlineCourses = Course::where('teacher_id', $teacherId)
            ->where('type', 'online')
            ->count();

        $centerCourses = Course::where('teacher_id', $teacherId)
            ->where('type', 'center')
            ->count();

        // عدد الطلاب
        $studentsCount = Student::where('teacher_id', $teacherId)->count();

        // الأرباح
        $profits = EnrollmentRequest::where('teacher_id', $teacherId)
            ->where('status', 'approved')
            ->sum('price');

        // الكوبونات المستخدمة
        $usedCoupons = PaymentCode::where('teacher_id', $teacherId)
            ->where('is_used', true)
            ->count();

        // الامتحانات
        $examsCount = Exam::where('teacher_id', $teacherId)
            ->where('type', 'exam')
            ->count();

        // الواجبات
        $assignmentsCount = Exam::where('teacher_id', $teacherId)
            ->where('type', 'assignment')
            ->count();

        // الترمات
        $semestersCount = Semester::where('teacher_id', $teacherId)->count();

        // الطلبات
        $requestsCount = EnrollmentRequest::where('teacher_id', $teacherId)->count();

        // الكتب
        $booksCount = Book::where('teacher_id', $teacherId)->count();

        /*
        |--------------------------------------------------------------------------
        | التقارير
        |--------------------------------------------------------------------------
        */

        // عدد الطلاب لكل شهر
        $studentsPerMonth = Student::where('teacher_id', $teacherId)
            ->selectRaw('YEAR(created_at) as year')
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // المحافظات
        $studentsByGovernorate = Student::where('teacher_id', $teacherId)
            ->whereNotNull('governorate')
            ->where('governorate', '!=', '')
            ->selectRaw('governorate, COUNT(*) as total')
            ->groupBy('governorate')
            ->orderByDesc('total')
            ->get();

        $studentsByRegion = Student::where('teacher_id', $teacherId)
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->selectRaw('region, COUNT(*) as total')
            ->groupBy('region')
            ->orderByDesc('total')
            ->get();

        // الجنس
        $studentsByGender = Student::where('teacher_id', $teacherId)
            ->whereNotNull('gender')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->get();

        // المرحلة الدراسية
        $studentsByStage = Student::query()
            ->join('stages', 'students.stage_id', '=', 'stages.id')
            ->where('students.teacher_id', $teacherId)
            ->selectRaw('stages.id, stages.name, COUNT(*) as total')
            ->groupBy('stages.id', 'stages.name')
            ->get();

        // اشتراكات الشهر الماضي
        $lastMonthSubscriptions = EnrollmentRequest::where('teacher_id', $teacherId)
            ->where('status', 'approved')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return response()->json([
            'data' => [
                'online_courses' => $onlineCourses,
                'center_courses' => $centerCourses,
                'students_count' => $studentsCount,
                'profits' => $profits,
                'used_coupons' => $usedCoupons,
                'exams_count' => $examsCount,
                'assignments_count' => $assignmentsCount,
                'semesters_count' => $semestersCount,
                'requests_count' => $requestsCount,
                'books_count' => $booksCount,

                'students_per_month' => $studentsPerMonth,
                'students_by_governorate' => $studentsByGovernorate,
                'students_by_region' => $studentsByRegion,
                'students_by_gender' => $studentsByGender,
                'students_by_stage' => $studentsByStage,

                'last_month_subscriptions' => [
                    'course' => $lastMonthSubscriptions['course'] ?? 0,
                    'semester' => $lastMonthSubscriptions['semester'] ?? 0,
                    'lesson' => $lastMonthSubscriptions['lesson'] ?? 0,
                ],
            ]
        ]);
    }

    public function adminReport()
    {
        $onlineCourses = Course::where('type', 'online')->count();

        $centerCourses = Course::where('type', 'center')->count();

        $studentsCount = Enrollment::distinct('student_id')
            ->count('student_id');

        $profits = EnrollmentRequest::where('status', 'approved')
            ->sum('price');

        $usedCoupons = PaymentCode::where('is_used', true)
            ->count();

        $examsCount = Exam::where('type', 'exam')->count();

        $assignmentsCount = Exam::where('type', 'assignment')->count();

        $semestersCount = Semester::count();

        $requestsCount = EnrollmentRequest::count();

        $booksCount = Book::count();

        $teachersCount = Teacher::count();

        return response()->json([
            'data' => [
                'teachers_count'    => $teachersCount,
                'online_courses'    => $onlineCourses,
                'center_courses'    => $centerCourses,
                'students_count'    => $studentsCount,
                'profits'           => $profits,
                'used_coupons'      => $usedCoupons,
                'exams_count'       => $examsCount,
                'assignments_count' => $assignmentsCount,
                'semesters_count'   => $semestersCount,
                'requests_count'    => $requestsCount,
                'books_count'       => $booksCount,
            ]
        ]);
    }

    public function monthlyProfitReport()
    {
        $profits = EnrollmentRequest::where('status', 'approved')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(price) as total_profit')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $data = $profits->map(function ($item) {

            $monthName = Carbon::create()
                ->month($item->month)
                ->format('F');

            return [
                'month' => $monthName . ' ' . $item->year,
                'profit' => (float) $item->total_profit,
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    public function teacherPdfReport(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);

        $from = $request->from ? Carbon::parse($request->from) : null;
        $to   = $request->to ? Carbon::parse($request->to) : null;

        // =========================
        // APPROVED REQUESTS ONLY (MASTER SOURCE)
        // =========================
        $approvedRequests = EnrollmentRequest::where('teacher_id', $teacherId)
            ->where('status', 'approved')
            ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
            ->get();

        // =========================
        // COURSES
        // =========================
        $courses = Course::where('teacher_id', $teacherId)->get();

        $onlineCourses = $courses->where('type', 'online')->count();
        $centerCourses = $courses->where('type', 'center')->count();

        $coursesData = $courses->map(function ($course) use ($approvedRequests) {

            $req = $approvedRequests->where('course_id', $course->id);

            return [
                'id' => $course->id,
                'title' => $course->title,
                'type' => $course->type,
                'students' => $req->pluck('student_id')->unique()->count(),
                'profit' => $req->sum('price'),
            ];
        });

        // =========================
        // SEMESTERS
        // =========================
        $semesters = Semester::where('teacher_id', $teacherId)->get();

        $semesterData = $semesters->map(function ($s) use ($approvedRequests) {

            $req = $approvedRequests->where('semester_id', $s->id);

            return [
                'name' => $s->name,
                'students' => $req->pluck('student_id')->unique()->count(),
                'profit' => $req->sum('price'),
            ];
        });

        // =========================
        // LESSONS
        // =========================
        $lessons = CourseDetail::whereHas('course', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->get();

        $lessonData = $lessons->map(function ($l) use ($approvedRequests) {

            $req = $approvedRequests->where('course_detail_id', $l->id);

            return [
                'title' => $l->title ?? 'Lesson',
                'students' => $req->pluck('student_id')->unique()->count(),
                'profit' => $req->sum('price'),
            ];
        });

        // =========================
        // TOTAL PROFIT
        // =========================
        $totalProfit = $approvedRequests->sum('price');

        // =========================
        // PDF GENERATION
        // =========================
        $pdf = PDF::loadView('reports.teacher_dashboard', [
            'teacher' => $teacher,
            'onlineCourses' => $onlineCourses,
            'centerCourses' => $centerCourses,
            'coursesData' => $coursesData,
            'semesterData' => $semesterData,
            'lessonData' => $lessonData,
            'totalProfit' => $totalProfit,
            'from' => $from,
            'to' => $to,
        ]);

        return $pdf->stream('teacher-report.pdf');
    }


    public function changePasswordStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'password' => 'required|min:6',
        ]);

        $student = Student::findOrFail($request->student_id);

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}

