<?php

use App\Http\Controllers\AccreditationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseTypeCotroller;
use App\Http\Controllers\CycleController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\KinshipController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MaritalStatusController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingContrller;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDegreeController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\StudentProgramController;
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

Route::group(['middleware' => 'auth:api'], function () {

  //Catalogs
  Route::get('roles', [RoleController::class, 'index']);
  Route::get('states', [StateController::class, 'index']);
  Route::get('municipalities', [MunicipalityController::class, 'index']);
  Route::get('marital_statuses', [MaritalStatusController::class, 'index']);
  Route::get('levels', [LevelController::class, 'index']);
  Route::get('accreditations', [AccreditationController::class, 'index']);
  Route::get('modalities', [ModalityController::class, 'index']);
  Route::get('shifts', [ShiftController::class, 'index']);
  Route::get('terms', [TermController::class, 'index']);
  Route::get('course_types', [CourseTypeCotroller::class, 'index']);
  Route::get('kinships', [KinshipController::class, 'index']);
  Route::get('document_types', [DocumentTypeController::class, 'index']);


  //Users
  // Route::post('users/dni', [UserController::class, 'getDni']);
  Route::apiResource('users', UserController::class);
  Route::group(['prefix' => 'users'], function () {
    Route::post('restore', [UserController::class, 'restore']);
  });

  //Settings
  Route::apiResource('settings', SettingContrller::class);

  //Campuses
  Route::apiResource('campuses', CampusController::class);
  Route::group(['prefix' => 'campuses'], function () {
    Route::post('restore', [CampusController::class, 'restore']);
  });

  //Programs
  Route::group(['prefix' => 'programs'], function () {
    //Courses
    Route::apiResource('courses', CourseController::class);
    Route::group(['prefix' => 'courses'], function () {
      Route::post('restore', [CourseController::class, 'restore']);
    });

    Route::post('restore', [ProgramController::class, 'restore']);
  });
  Route::apiResource('programs', ProgramController::class);

  //Teachers
  Route::apiResource('teachers', TeacherController::class);
  Route::group(['prefix' => 'teachers'], function () {
    Route::post('restore', [TeacherController::class, 'restore']);
  });

  //Cycles
  Route::apiResource('cycles', CycleController::class);
  Route::group(['prefix' => 'cycles'], function () {
    Route::post('restore', [CycleController::class, 'restore']);
  });

  //Students
  Route::group(['prefix' => 'students'], function () {
    //Student degrees
    Route::apiResource('student_degrees', StudentDegreeController::class);
    Route::group(['prefix' => 'student_degrees'], function () {
      Route::post('restore', [StudentDegreeController::class, 'restore']);
    });

    //Student documents
    Route::apiResource('student_documents', StudentDocumentController::class);
    Route::group(['prefix' => 'student_documents'], function () {
      Route::post('restore', [StudentDocumentController::class, 'restore']);
    });

    //Student documents
    Route::apiResource('student_programs', StudentProgramController::class);
    Route::group(['prefix' => 'student_programs'], function () {
      Route::post('restore', [StudentProgramController::class, 'restore']);
    });

    Route::post('restore', [StudentController::class, 'restore']);
  });
  Route::apiResource('students', StudentController::class);
});
