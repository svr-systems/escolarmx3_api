<?php

use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseTypeCotroller;
use App\Http\Controllers\CycleController;
use App\Http\Controllers\InstitutionContrller;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MaritalStatusController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('login', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'login']);

// Route::get('credential/{user_id}', [ImageController::class, 'UserDNI']);

Route::group(['middleware' => 'auth:api'], function () {

    //Catalogs
    Route::get('roles', [RoleController::class, 'index']);
    Route::get('states', [StateController::class, 'index']);
    Route::get('municipalities', [MunicipalityController::class, 'index']);
    Route::get('maritals_statuses', [MaritalStatusController::class, 'index']);
    Route::get('levels', [LevelController::class, 'index']);
    Route::get('accreditations', [AccreditationController::class, 'index']);
    Route::get('modalities', [ModalityController::class, 'index']);
    Route::get('shifts', [ShiftController::class, 'index']);
    Route::get('terms', [TermController::class, 'index']);
    Route::get('course_types', [CourseTypeCotroller::class, 'index']);


    //Users
    // Route::post('users/dni', [UserController::class, 'getDni']);
    Route::apiResource('users', UserController::class);

    //Locations
    Route::apiResource('institutions/campuses', CampusController::class);

    //Institutions
    Route::apiResource('institutions', InstitutionContrller::class);

    //Teachers
    Route::apiResource('teachers', TeacherController::class);

    //Courses
    Route::apiResource('programs/courses', CourseController::class);

    //Programs
    Route::apiResource('programs', ProgramController::class);

    //Cycles
    Route::apiResource('cycles', CycleController::class);

});