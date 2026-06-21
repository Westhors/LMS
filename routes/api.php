<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssistantTeacherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CenterHourController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseDetailController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ExamAnswerController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PaymentCodeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AdminController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('admin/logout', [AdminController::class, 'logout']);
    Route::get('admin/check-auth', [AdminController::class, 'checkAuth']);
});


//////////////////////////////////////////////////////////Stage//////////////////////////////////////

Route::post('stage/index', [StageController::class, 'index']);
Route::post('stage/restore', [StageController::class, 'restore']);
Route::delete('stage/delete', [StageController::class, 'destroy']);
Route::delete('stage/force-delete', [StageController::class, 'forceDelete']);
Route::post('stage/update/{stage}', [StageController::class, 'forceUpdate']);
Route::put('/stage/{id}/{column}', [StageController::class, 'toggle']);
Route::apiResource('stage', StageController::class);

//////////////////////////////////////////////////////////Stage//////////////////////////////////////


//////////////////////////////////////////////////////////Subject//////////////////////////////////////

Route::post('subject/index', [SubjectController::class, 'index']);
Route::post('index-subject/index', [SubjectController::class, 'indexSubject']);
Route::post('subject/restore', [SubjectController::class, 'restore']);
Route::delete('subject/delete', [SubjectController::class, 'destroy']);
Route::delete('subject/force-delete', [SubjectController::class, 'forceDelete']);
Route::post('subject/update/{subject}', [SubjectController::class, 'forceUpdate']);
Route::put('/subject/{id}/{column}', [SubjectController::class, 'toggle']);
Route::apiResource('subject', SubjectController::class);

//////////////////////////////////////////////////////////Subject//////////////////////////////////////

//////////////////////////////////////////////////////////teacher//////////////////////////////////////

Route::post('teacher/index', [TeacherController::class, 'index']);
Route::post('teacher/restore', [TeacherController::class, 'restore']);
Route::delete('teacher/delete', [TeacherController::class, 'destroy']);
Route::delete('teacher/force-delete', [TeacherController::class, 'forceDelete']);
Route::post('teacher/update/{teacher}', [TeacherController::class, 'forceUpdate']);
Route::put('/teacher/{id}/{column}', [TeacherController::class, 'toggle']);

Route::apiResource('teacher', TeacherController::class);

// IMPORTANT: keep this LAST
Route::get('/{teacher:sub_domain}', [TeacherController::class, 'subDomain']);

//////////////////////////////////////////////////////////teacher//////////////////////////////////////


//////////////////////////////////////////////////////////assistantteacher//////////////////////////////////////

Route::post('assistant-teacher/index', [AssistantTeacherController::class, 'index']);
Route::post('assistant-teacher/restore', [AssistantTeacherController::class, 'restore']);
Route::delete('assistant-teacher/delete', [AssistantTeacherController::class, 'destroy']);
Route::delete('assistant-teacher/force-delete', [AssistantTeacherController::class, 'forceDelete']);
Route::post('assistant-teacher/update/{assistantTeacher}', [AssistantTeacherController::class, 'forceUpdate']);
Route::put('/assistant-teacher/{id}/{column}', [AssistantTeacherController::class, 'toggle']);
Route::apiResource('assistant-teacher', AssistantTeacherController::class);

//////////////////////////////////////////////////////////teacher//////////////////////////////////////




//////////////////////////////////////////////////////////home//////////////////////////////////////

Route::post('home/index', [HomeController::class, 'index']);
Route::post('home/restore', [HomeController::class, 'restore']);
Route::delete('home/delete', [HomeController::class, 'destroy']);
Route::delete('home/force-delete', [HomeController::class, 'forceDelete']);
Route::post('home/update/{home}', [HomeController::class, 'forceUpdate']);
Route::put('/home/{id}/{column}', [HomeController::class, 'toggle']);
Route::apiResource('home', HomeController::class);

//////////////////////////////////////////////////////////home//////////////////////////////////////




//////////////////////////////////////////////////////////course//////////////////////////////////////

Route::post('course/index', [CourseController::class, 'index']);
Route::post('course/restore', [CourseController::class, 'restore']);
Route::delete('course/delete', [CourseController::class, 'destroy']);
Route::delete('course/force-delete', [CourseController::class, 'forceDelete']);
Route::post('course/update/{course}', [CourseController::class, 'forceUpdate']);
Route::put('/course/{id}/{column}', [CourseController::class, 'toggle']);
Route::apiResource('course', CourseController::class);

//////////////////////////////////////////////////////////course//////////////////////////////////////


//////////////////////////////////////////////////////////course-detail//////////////////////////////////////
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('course-detail/index', [CourseDetailController::class, 'index']);
    Route::post('course-detail/restore', [CourseDetailController::class, 'restore']);
    Route::delete('course-detail/delete', [CourseDetailController::class, 'destroy']);
    Route::delete('course-detail/force-delete', [CourseDetailController::class, 'forceDelete']);
    Route::post('course-detail/update/{courseDetail}', [CourseDetailController::class, 'forceUpdate']);
    Route::put('/course-detail/{id}/{column}', [CourseDetailController::class, 'toggle']);
    Route::apiResource('course-detail', CourseDetailController::class);
});
//////////////////////////////////////////////////////////course-detail//////////////////////////////////////


//////////////////////////////////////////////////////////book//////////////////////////////////////

Route::post('book/index', [BookController::class, 'index']);
Route::post('book/restore', [BookController::class, 'restore']);
Route::delete('book/delete', [BookController::class, 'destroy']);
Route::delete('book/force-delete', [BookController::class, 'forceDelete']);
Route::post('book/update/{book}', [BookController::class, 'forceUpdate']);
Route::put('/book/{id}/{column}', [BookController::class, 'toggle']);
Route::apiResource('book', BookController::class);

//////////////////////////////////////////////////////////book//////////////////////////////////////



//////////////////////////////////////////////////////////exam//////////////////////////////////////

Route::post('exam/index', [ExamController::class, 'index']);
Route::post('bank-questions/index', [ExamController::class, 'bankIndex']);
Route::post('exam/restore', [ExamController::class, 'restore']);
Route::delete('exam/delete', [ExamController::class, 'destroy']);
Route::delete('exam/force-delete', [ExamController::class, 'forceDelete']);
Route::post('exam/update/{exam}', [ExamController::class, 'forceUpdate']);
Route::put('/exam/{id}/{column}', [ExamController::class, 'toggle']);
Route::apiResource('exam', ExamController::class);

Route::post('exam/add-questions', [ExamController::class, 'addQuestions']); // New route for
Route::post('exam/submit', [ExamController::class, 'submitExam']); // New route for submitting exam answers
Route::post('exam/grade-essay', [ExamController::class, 'gradeEssay']); // New route for grading essay answers
Route::get('exam/result/{examId}/{studentId}', [ExamController::class, 'result']);
Route::get('/exams/{examId}/questions', [ExamController::class, 'getQuestions']);
//////////////////////////////////////////////////////////exam//////////////////////////////////////



//////////////////////////////////////////////////////////Feature//////////////////////////////////////

Route::post('feature/index', [FeatureController::class, 'index']);
Route::post('feature/restore', [FeatureController::class, 'restore']);
Route::delete('feature/delete', [FeatureController::class, 'destroy']);
Route::delete('feature/force-delete', [FeatureController::class, 'forceDelete']);
Route::post('feature/update/{feature}', [FeatureController::class, 'forceUpdate']);
Route::put('/feature/{id}/{column}', [FeatureController::class, 'toggle']);
Route::apiResource('feature', FeatureController::class);

//////////////////////////////////////////////////////////Feature//////////////////////////////////////


//////////////////////////////////////////////////////////About//////////////////////////////////////

Route::post('about/index', [AboutController::class, 'index']);
Route::post('about/restore', [AboutController::class, 'restore']);
Route::delete('about/delete', [AboutController::class, 'destroy']);
Route::delete('about/force-delete', [AboutController::class, 'forceDelete']);
Route::post('about/update/{about}', [AboutController::class, 'forceUpdate']);
Route::put('/about/{id}/{column}', [AboutController::class, 'toggle']);
Route::apiResource('about', AboutController::class);

//////////////////////////////////////////////////////////About//////////////////////////////////////


//////////////////////////////////////////////////////////Footer//////////////////////////////////////

Route::post('footer/index', [FooterController::class, 'index']);
Route::post('footer/restore', [FooterController::class, 'restore']);
Route::delete('footer/delete', [FooterController::class, 'destroy']);
Route::delete('footer/force-delete', [FooterController::class, 'forceDelete']);
Route::post('footer/update/{footer}', [FooterController::class, 'forceUpdate']);
Route::put('/footer/{id}/{column}', [FooterController::class, 'toggle']);
Route::apiResource('footer', FooterController::class);

//////////////////////////////////////////////////////////Footer//////////////////////////////////////


//////////////////////////////////////////////////////////CenterHour//////////////////////////////////////

Route::post('center-hour/index', [CenterHourController::class, 'index']);
Route::post('center-hour/restore', [CenterHourController::class, 'restore']);
Route::delete('center-hour/delete', [CenterHourController::class, 'destroy']);
Route::delete('center-hour/force-delete', [CenterHourController::class, 'forceDelete']);
Route::post('center-hour/update/{centerHour}', [CenterHourController::class, 'forceUpdate']);
Route::put('/center-hour/{id}/{column}', [CenterHourController::class, 'toggle']);
Route::apiResource('center-hour', CenterHourController::class);

//////////////////////////////////////////////////////////CenterHour//////////////////////////////////////



//////////////////////////////////////////////////////////Semester//////////////////////////////////////

Route::post('semesters/index', [SemesterController::class, 'index']);
Route::post('semesters/restore', [SemesterController::class, 'restore']);
Route::delete('semesters/delete', [SemesterController::class, 'destroy']);
Route::delete('semesters/force-delete', [SemesterController::class, 'forceDelete']);
Route::put('/semesters/{id}/{column}', [SemesterController::class, 'toggle']);
Route::apiResource('semesters', SemesterController::class);

//////////////////////////////////////////////////////////Semester//////////////////////////////////////



//////////////////////////////////////////////////////////Payment Code//////////////////////////////////////

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('generate-codes', [PaymentCodeController::class, 'generateCodes']);
    Route::post('payment-code/index', [PaymentCodeController::class, 'index']);
});


//////////////////////////////////////////////////////////Payment Code//////////////////////////////////////


////////////////////////////////////////// media ////////////////////////////////
Route::group(['middleware' => ['api']], static function () {
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{media}', [MediaController::class, 'show']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);
    Route::get('/get-unused-media', [MediaController::class, 'getUnUsedImages']);
    Route::delete('/delete-unused-media', [MediaController::class, 'deleteUnUsedImages']);
});
Route::get('/get-media/{media}', [MediaController::class, 'show']);
Route::post('/media-array', [MediaController::class, 'showMedia']);
Route::post('/media-upload-many', [MediaController::class, 'storeMany']);
//////////////////////////////////////// media ////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::post('student/login', [StudentController::class, 'login']);
Route::post('student/application-form', [StudentController::class, 'applicationForm']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('student/logout', [StudentController::class, 'logout']);
    Route::get('student/check-auth', [StudentController::class, 'checkAuth']);
});


//////////////////////////////////////////////////////////student //////////////////////////////////////

Route::post('student/index', [StudentController::class, 'index']);
Route::post('student/restore', [StudentController::class, 'restore']);
Route::delete('student/delete', [StudentController::class, 'destroy']);
Route::delete('student/force-delete', [StudentController::class, 'forceDelete']);
Route::post('student/update/{student}', [StudentController::class, 'forceUpdate']);
Route::put('/student/{id}/{column}', [StudentController::class, 'toggle']);
Route::apiResource('student', StudentController::class);

//////////////////////////////////////////////////////////Student//////////////////////////////////////


//////////////////////////////////////////////////////////offer //////////////////////////////////////

Route::post('offer/index', [OfferController::class, 'index']);
Route::post('offer/restore', [OfferController::class, 'restore']);
Route::delete('offer/delete', [OfferController::class, 'destroy']);
Route::delete('offer/force-delete', [OfferController::class, 'forceDelete']);
Route::post('offer/update/{offer}', [OfferController::class, 'forceUpdate']);
Route::put('/offer/{id}/{column}', [OfferController::class, 'toggle']);
Route::apiResource('offer', OfferController::class);

//////////////////////////////////////////////////////////Offer//////////////////////////////////////


//////////////////////////////////////////////////////////Enrollment//////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
    // 🎓 الطالب يطلب شراء / أو شراء مباشر
    Route::post('/enroll/request', [EnrollmentController::class, 'requestEnroll']);
    // 🎟️ الطالب يستخدم كود
    Route::post('/enroll/redeem-code', [EnrollmentController::class, 'redeemCode']);
    // 💰 الطالب يستخدم كود wallet
    Route::post('/wallet/redeem', [EnrollmentController::class, 'redeemWallet']);
    // 🧑‍🏫 المدرس يشوف الطلبات
    Route::get('/requests-redeem/teacher', [EnrollmentController::class, 'teacherRequests']);
    // ❌ رفض طلب
    Route::post('/request/teacher/{id}/status', [EnrollmentController::class, 'status']);
    Route::post('lessons/{lessonId}/attendance',[CourseDetailController::class, 'markAttendance']);
});
//////////////////////////////////////////////////////////Enrollment//////////////////////////////////////


//////////////////////////////////////////////////////////my student//////////////////////////////////////
Route::get('/my-student/learn/{id}', [EnrollmentController::class, 'studentLearning']);
Route::get('/my-student/learn', [EnrollmentController::class, 'myLearning'])
    ->middleware('auth:sanctum');
//////////////////////////////////////////////////////////my student//////////////////////////////////////



//////////////////////////////////////////////////////////permissions//////////////////////////////////////
Route::get('access-control/permissions',[PermissionController::class, 'allPermission']);
Route::get('show-permissions',[PermissionController::class, 'showPermissions']);
Route::post('assistant/permissions',[PermissionController::class, 'assignPermission']);


//////////////////////////////////////////////////////////permissions//////////////////////////////////////



//////////////////////////////////////////////////////////diffrent request//////////////////////////////////////
Route::get('teachers/monthly-profit-report', [TeacherController::class, 'monthlyProfitReport']);
Route::post('/activate/theme', [TeacherController::class, 'activateTheme']);
Route::post('/teachers/theme', [TeacherController::class, 'getTeacherTheme']);
Route::get('teachers/{teacher}/report', [TeacherController::class, 'teacherReport']);
Route::get('admin/report', [TeacherController::class, 'adminReport']);
Route::get('teachers/{teacher}/report/pdf', [TeacherController::class, 'teacherPdfReport']);

Route::post('student/change-password', [TeacherController::class, 'changePasswordStudent']);
Route::get('payment-codes/report', [PaymentCodeController::class, 'paymentCodesReport']);
Route::post('/pass-student', [ExamAnswerController::class, 'passStudent']);
Route::post('/course-detail-attendance', [StudentController::class, 'showAttendance']);
Route::post('/all/course-detail-attendance', [StudentController::class, 'allAttendance']);


//////////////////////////////////////////////////////////diffrent request//////////////////////////////////////
