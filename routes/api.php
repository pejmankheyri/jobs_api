<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CompanyImagesController;
use App\Http\Controllers\Api\V1\CompanyTagController;
use App\Http\Controllers\Api\V1\JobItemController;
use App\Http\Controllers\Api\V1\JobTagController;
use App\Http\Controllers\Api\V1\SavedJobController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\UserAvatarController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserCvController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('reset-password');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password')->middleware('auth:sanctum');

    // USERS
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('auth:sanctum', 'role:admin');
        Route::get('/me', [UserController::class, 'show'])->name('show')->middleware('auth:sanctum');
        Route::put('/', [UserController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum', 'role:admin');
        Route::get('/jobs', [UserController::class, 'jobs'])->name('jobs')->middleware('auth:sanctum');
        Route::get('/companies', [UserController::class, 'companies'])->name('companies')->middleware('auth:sanctum');
        Route::post('/avatar', [UserAvatarController::class, 'uploadAvatar'])->name('upload-avatar')->middleware('auth:sanctum');
        Route::get('/{id}/avatar', [UserAvatarController::class, 'getAvatar'])->name('avatar')->middleware('auth:sanctum');
        Route::post('/cv', [UserCvController::class, 'uploadCV'])->name('cv')->middleware('auth:sanctum');
    });

    // JOBS
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobItemController::class, 'index'])->name('index');
        Route::get('/saved-jobs', [SavedJobController::class, 'index'])->middleware('auth:sanctum')->name('saved');

        Route::get('/{id}', [JobItemController::class, 'show'])->name('show');
        Route::post('/', [JobItemController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [JobItemController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [JobItemController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
        Route::post('/{id}/apply', [JobItemController::class, 'apply'])->name('apply')->middleware('auth:sanctum');
        Route::get('/{id}/applicants', [JobItemController::class, 'applicants'])->name('applicants')->middleware('auth:sanctum');
        Route::post('/{job}/save', [SavedJobController::class, 'store'])->middleware('auth:sanctum');
        Route::delete('/{job}/unsave', [SavedJobController::class, 'destroy'])->middleware('auth:sanctum');
    });

    // COMPANIES
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/{id}', [CompanyController::class, 'show'])->name('show');
        Route::post('/', [CompanyController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [CompanyController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
        Route::post('/{id}/images', [CompanyImagesController::class, 'store'])->name('images.store');
        Route::delete('/image/{id}', [CompanyImagesController::class, 'destroy'])->name('images.destroy');
        Route::post('/{id}/logo', [CompanyController::class, 'uploadLogo'])->name('logo')->middleware('auth:sanctum');
    });

    // TAGS
    Route::get('/jobs/tags/{tagId}', [JobTagController::class, 'index']);
    Route::get('/companies/tags/{tagId}', [CompanyTagController::class, 'index']);

    // SEARCH
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/jobs', [SearchController::class, 'jobs'])->name('jobs-search');
        Route::get('/locations', [SearchController::class, 'locations'])->name('locations-search');
    });

});
