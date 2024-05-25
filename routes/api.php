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
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [UserController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum', 'role:admin');
    });

    // JOBS
    Route::prefix('jobs')->name('jobs.')->middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('/', JobItemController::class)->except(['index', 'show']);
    });
    Route::apiResource('jobs', JobItemController::class)->only(['index', 'show']);

    // COMPANIES
    Route::prefix('companies')->name('companies.')->middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('/', CompanyController::class)->except(['index', 'show']);
        Route::post('/{id}/images', [CompanyImagesController::class, 'store'])->name('images.store');
        Route::delete('/image/{id}', [CompanyImagesController::class, 'destroy'])->name('images.destroy');
    });
    Route::apiResource('companies', CompanyController::class)->only(['index', 'show']);


    // TAGS
    Route::get('/jobs/tags/{tagId}', [JobTagController::class, 'index']);
    Route::get('/companies/tags/{tagId}', [CompanyTagController::class, 'index']);

});


