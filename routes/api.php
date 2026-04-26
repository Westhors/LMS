<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssistantTeacherController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseDetailController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\StageController;
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

Route::post('course-detail/index', [CourseDetailController::class, 'index']);
Route::post('course-detail/restore', [CourseDetailController::class, 'restore']);
Route::delete('course-detail/delete', [CourseDetailController::class, 'destroy']);
Route::delete('course-detail/force-delete', [CourseDetailController::class, 'forceDelete']);
Route::post('course-detail/update/{courseDetail}', [CourseDetailController::class, 'forceUpdate']);
Route::put('/course-detail/{id}/{column}', [CourseDetailController::class, 'toggle']);
Route::apiResource('course-detail', CourseDetailController::class);

//////////////////////////////////////////////////////////course-detail//////////////////////////////////////



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


