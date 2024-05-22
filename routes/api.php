<?php

use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\CompanyTagController;
use App\Http\Controllers\Api\V1\JobItemController;
use App\Http\Controllers\Api\V1\JobTagController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('jobs', JobItemController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::get('/jobs/tags/{tagId}', [JobTagController::class, 'index']);
    Route::get('/companies/tags/{tagId}', [CompanyTagController::class, 'index']);
});


