<?php

use App\Constants\Roles;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Gestor\CareerController;
use App\Http\Controllers\Gestor\EnrollmentsController;
use App\Http\Controllers\Gestor\SpecializationController;
use App\Http\Controllers\Gestor\SubjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register',[AuthController::class,"register"]);
Route::post('login',[AuthController::class,"login"]);

//Rutas protegidas por autenticación (Solo usuarios logeados pueden acceder)
Route::middleware('auth:sanctum')->group(function () {
    //Rutas que solo pueden acceder los administradores
    Route::middleware('abilities:' . Roles::ADMIN)->group(function (){
        Route::apiResource('users',UserController::class);       //Todo el apiResource de users, los usuarios regularas usaran otras rutas para ver y actualizar sus datos.
    });

    //Ruta que solo pueden acceder los cadetes
    Route::middleware('abilities:' . Roles::CADET)->group(function (){

    });

    //Ruta que cualquier usuario autenticado puede acceder
    Route::get("progress/{code}",[SubjectController::class,"getStudentProgress"]);
    Route::post('onboarding/complete',[AuthController::class,"completeOnboarding"]);
    Route::post('logout',[AuthController::class,"logout"]);
}); 

Route::get("careers/{career}/specializations",[SpecializationController::class,"getByCareer"]);
Route::apiResource('careers',CareerController::class);

Route::get("specializations/{specialization}/subjects",[SubjectController::class,"getBySpecialization"]);
Route::apiResource('specializations',SpecializationController::class);

Route::apiResource('subjects',SubjectController::class);

Route::apiResource('enrollments',EnrollmentsController::class);

Route::get('users/{code}/enrollments',[SubjectController::class,"getEnrolled"]);
