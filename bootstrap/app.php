<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => __('message.resource_not_found'),
                ], 404);
            }
            if ($exception instanceof AccessDeniedHttpException) {
                return response()->json([
                    'message' => __('message.access_denied'),
                ], 404);
            }
            if ($exception instanceof RouteNotFoundException) {
                return response()->json([
                    'message' => __('message.route_not_found'),
                ], 500);
            }

        });
    })->create();
