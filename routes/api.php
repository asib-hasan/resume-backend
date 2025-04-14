<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    #experience
    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::post('/experiences', [ExperienceController::class, 'store']);
    Route::put('/experiences/{id}', [ExperienceController::class, 'update']);
    Route::delete('/experiences/{id}', [ExperienceController::class, 'destroy']);
    Route::post('/update/experience/order', [ExperienceController::class, 'sort_order']);

    #education
    Route::get('/educations', [EducationController::class, 'index']);
    Route::post('/educations', [EducationController::class, 'store']);
    Route::put('/educations/{id}', [EducationController::class, 'update']);
    Route::delete('/educations/{id}', [EducationController::class, 'destroy']);
    Route::post('/update/education/order', [EducationController::class, 'sort_order']);
});
