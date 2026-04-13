<?php

use App\Constants\Roles;
use App\Http\Controllers\AulaVirtual\AssignmentController;
use App\Http\Controllers\AulaVirtual\ClassroomController;
use App\Http\Controllers\AulaVirtual\PostController;
use App\Http\Controllers\AulaVirtual\SubmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Gestor\AcademicRecordController;
use App\Http\Controllers\Gestor\CareerController;
use App\Http\Controllers\Gestor\EnrollmentsController;
use App\Http\Controllers\Gestor\SpecializationController;
use App\Http\Controllers\Gestor\SubjectController;
use App\Http\Controllers\UserController;
use App\Models\AulaVirtual\Classroom;
use Illuminate\Support\Facades\Route;

Route::post('register',[AuthController::class,"register"]);
Route::post('login',[AuthController::class,"login"]);
Route::post('onboarding/complete',[AuthController::class,"completeOnboarding"]);


//Rutas protegidas por autenticación (Solo usuarios logeados pueden acceder)
Route::middleware('auth:sanctum')->group(function () {
    //Rutas que cualquier usuario autenticado puede acceder

    Route::post('/classroom/join',[ClassroomController::class , 'join']);
    Route::post('/classroom/{classroom}/leave',[ClassroomController::class,'leave']);

    //Rutas que solo pueden acceder los administradores
    Route::middleware('ability:' . Roles::ADMIN)->prefix('admin')->group(function (){
        Route::put("users/{user}/academic_records/bulk",[AcademicRecordController::class,"bulkUpdate"]);
        Route::apiResource('users',UserController::class);       //Todo el apiResource de users, los usuarios regularas usaran otras rutas para ver y actualizar sus datos.
    });

    //Rutas que solo pueden acceder los profesores
    Route::middleware('ability:' . Roles::PROFESSOR . "," . Roles::ADMIN)->group(function (){
        
        Route::post('/classroom/{classroom}/kick/{user}',[ClassroomController::class,'kick']);
        Route::apiResource('classrooms',ClassroomController::class)->except(['index','show']);
        Route::apiResource('classrooms.assignments',AssignmentController::class)->except(['index','show'])
                                                                                ->scoped(['assignments' => 'id'])
                                                                                ->shallow();
        Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'grade']);
        Route::apiResource('assignments.submissions',SubmissionController::class)->only(['index'])
                                                                                 ->scoped(['submissions'=>'id'])
                                                                                 ->shallow();
        Route::apiResource('classrooms.posts',PostController::class)->except(['index','show'])
                                                                    ->scoped(['posts'=>'id'])
                                                                    ->shallow();                                                                                                         
    });

    //Rutas que solo pueden acceder los cadetes
    Route::middleware('ability:' . Roles::CADET . "," . Roles::ADMIN)->group(function (){

    });

    //Ruta que cualquier usuario autenticado puede acceder
    Route::apiResource('classrooms',ClassroomController::class)->only(['index','show']);
    Route::apiResource('classrooms.assignments',AssignmentController::class)->only(['index','show'])
                                                                            ->scoped(['assignments' => 'id'])
                                                                            ->shallow();
    Route::apiResource('assignments.submissions',SubmissionController::class)->except(['index'])
                                                                             ->scoped(['submissions'=>'id'])
                                                                             ->shallow(); 
    Route::apiResource('classrooms.posts',PostController::class)->only(['index','show'])
                                                                ->scoped(['posts'=>'id'])
                                                                ->shallow();                                                                    
    
    
    Route::get("progress/{code}",[SubjectController::class,"getStudentProgress"]);
    Route::post('logout',[AuthController::class,"logout"]);
}); 

Route::get("careers/{career}/specializations",[SpecializationController::class,"getByCareer"]);
Route::apiResource('careers',CareerController::class);

Route::get("specializations/{specialization}/subjects",[SubjectController::class,"getBySpecialization"]);
Route::apiResource('specializations',SpecializationController::class);

Route::apiResource('subjects',SubjectController::class);

Route::apiResource('enrollments',EnrollmentsController::class);

Route::get('users/{code}/enrollments',[SubjectController::class,"getEnrolled"]);
