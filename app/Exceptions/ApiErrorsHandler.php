<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;

class ApiErrorsHandler extends Exception
{
    public function render($request, Exception $exception)
    {
        dd($request);
        if ($request->expectsJson() && $exception instanceof ModelNotFoundException) {
            return Route::respondWithRoute('api.fallback');
        }

        dd($exception->getMessage());
        // return parent::render($request, $exception);
    }
}
