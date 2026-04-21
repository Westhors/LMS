<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssistantTeacherController;
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


Route::post('admin/login', [AdminController::class, 'login']);
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




//////////////////////////////////////////////////////////home//////////////////////////////////////

Route::post('home/index', [HomeController::class, 'index']);
Route::post('home/restore', [HomeController::class, 'restore']);
Route::delete('home/delete', [HomeController::class, 'destroy']);
Route::delete('home/force-delete', [HomeController::class, 'forceDelete']);
Route::post('home/update/{home}', [HomeController::class, 'forceUpdate']);
Route::put('/home/{id}/{column}', [HomeController::class, 'toggle']);
Route::apiResource('home', HomeController::class);

//////////////////////////////////////////////////////////home//////////////////////////////////////





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


