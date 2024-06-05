<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CompanyImagesController;
use App\Http\Controllers\Api\V1\CompanyTagController;
use App\Http\Controllers\Api\V1\JobItemController;
use App\Http\Controllers\Api\V1\JobTagController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


    // USERS
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('auth:sanctum', 'role:admin');
        Route::get('/{id}', [UserController::class, 'show'])->name('show')->middleware('auth:sanctum');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{id}', [UserController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum', 'role:admin');
        Route::get('/{id}/jobs', [UserController::class, 'jobs'])->name('jobs')->middleware('auth:sanctum');
        Route::post('/avatar', [UserController::class, 'uploadAvatar'])->name('avatar')->middleware('auth:sanctum');
        Route::get('/{id}/avatar', [UserController::class, 'getAvatar'])->name('avatar')->middleware('auth:sanctum');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password')->middleware('auth:sanctum');
    });

    // JOBS
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobItemController::class, 'index'])->name('index');
        Route::get('/{id}', [JobItemController::class, 'show'])->name('show');
        Route::post('/', [JobItemController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [JobItemController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [JobItemController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
        Route::post('/{id}/apply', [JobItemController::class, 'apply'])->name('apply')->middleware('auth:sanctum');
        Route::get('/{id}/applicants', [JobItemController::class, 'applicants'])->name('applicants')->middleware('auth:sanctum');
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
    });


    // TAGS
    Route::get('/jobs/tags/{tagId}', [JobTagController::class, 'index']);
    Route::get('/companies/tags/{tagId}', [CompanyTagController::class, 'index']);

});


