<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});


Route::get('teachers/{teacher}/report/pdf', [TeacherController::class, 'teacherPdfReport']);
