<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\SkillsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AwardsController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    #profile
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);

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

    #skills
    Route::get('/skills', [SkillsController::class, 'index']);
    Route::post('/skills', [SkillsController::class, 'store']);
    Route::put('/skills/{id}', [SkillsController::class, 'update']);
    Route::delete('/skills/{id}', [SkillsController::class, 'destroy']);
    Route::post('/update/skill/order', [SkillsController::class, 'sort_order']);

    #interest
    Route::get('/interests', [InterestController::class, 'index']);
    Route::post('/interests', [InterestController::class, 'store']);
    Route::put('/interests/{id}', [InterestController::class, 'update']);
    Route::delete('/interests/{id}', [InterestController::class, 'destroy']);
    Route::post('/update/interest/order', [InterestController::class, 'sort_order']);

    #publication
    Route::get('/publications', [PublicationController::class, 'index']);
    Route::post('/publications', [PublicationController::class, 'store']);
    Route::put('/publications/{id}', [PublicationController::class, 'update']);
    Route::delete('/publications/{id}', [PublicationController::class, 'destroy']);
    Route::post('/update/publication/order', [PublicationController::class, 'sort_order']);

    #language
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::post('/languages', [LanguageController::class, 'store']);
    Route::put('/languages/{id}', [LanguageController::class, 'update']);
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);
    Route::post('/update/language/order', [LanguageController::class, 'sort_order']);

    #certificate
    Route::get('/certifications', [CertificateController::class, 'index']);
    Route::post('/certifications', [CertificateController::class, 'store']);
    Route::put('/certifications/{id}', [CertificateController::class, 'update']);
    Route::delete('/certifications/{id}', [CertificateController::class, 'destroy']);
    Route::post('/update/certification/order', [CertificateController::class, 'sort_order']);

    #award
    Route::get('/awards', [AwardsController::class, 'index']);
    Route::post('/awards', [AwardsController::class, 'store']);
    Route::put('/awards/{id}', [AwardsController::class, 'update']);
    Route::delete('/awards/{id}', [AwardsController::class, 'destroy']);
    Route::post('/update/award/order', [AwardsController::class, 'sort_order']);

    #blog
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/single/blog/{id}', [BlogController::class, 'get_single']);
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
});
