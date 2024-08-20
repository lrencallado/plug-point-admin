<?php

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                    'status' => \App\Enums\Api\V1\ApiResponseCode::BAD_REQUEST,
                ], $exception->status);
            }
        });

        $exceptions->render(function (NotFoundHttpException $exception) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'errors' => [],
                    'status' => \App\Enums\Api\V1\ApiResponseCode::NOT_FOUND,
                ], $exception->getStatusCode());
            }
        });
    })->create();
