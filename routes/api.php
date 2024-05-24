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

    Route::apiResource('users', UserController::class)->middleware('auth:sanctum');

    // Route::apiResource('jobs', JobItemController::class);
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobItemController::class, 'index'])->name('index');
        Route::get('/{id}', [JobItemController::class, 'show'])->name('show');
        Route::post('/', [JobItemController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [JobItemController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [JobItemController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
    });

    // Route::apiResource('companies', CompanyController::class);
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/{id}', [CompanyController::class, 'show'])->name('show');
        Route::post('/', [CompanyController::class, 'store'])->name('store')->middleware('auth:sanctum');
        Route::put('/{id}', [CompanyController::class, 'update'])->name('update')->middleware('auth:sanctum');
        Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('destroy')->middleware('auth:sanctum');
        Route::post('/{id}/images', [CompanyImagesController::class, 'store'])->name('images.store')->middleware('auth:sanctum');
        Route::delete('/image/{id}', [CompanyImagesController::class, 'destroy'])->name('images.destroy')->middleware('auth:sanctum');

    });

    Route::get('/jobs/tags/{tagId}', [JobTagController::class, 'index']);
    Route::get('/companies/tags/{tagId}', [CompanyTagController::class, 'index']);

});


