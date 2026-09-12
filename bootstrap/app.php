<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException; // Added for Auth handling
use Illuminate\Http\Exceptions\ThrottleRequestsException; // Added for Rate Limit handling
// use Throwable; // Required for the exception handler

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Set back to 60 for final submission, keep 3 for testing
        $middleware->throttleApi('60,1'); 

        // This stops the redirect and returns a 401 JSON response for APIs
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return null;
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 1. Handle Rate Limiting Errors professionally
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many requests. Please slow down.',
                    'retry_after_seconds' => $e->getHeaders()['Retry-After'] ?? null
                ], 429);
            }
        });

        // 2. Handle Authentication Errors professionally
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated. A valid Bearer token is required.'
                ], 401);
            }
        });

        // 3. Ensure all other exceptions in the API return JSON
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

    })->create();