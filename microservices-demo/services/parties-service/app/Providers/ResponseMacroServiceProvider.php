<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register response macros.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = null, $message = 'Success', $statusCode = 200) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('error', function ($message = 'Error', $statusCode = 400, $errors = null) {
            $response = [
                'success' => false,
                'message' => $message,
            ];

            if ($errors) {
                $response['errors'] = $errors;
            }

            return response()->json($response, $statusCode);
        });

        Response::macro('notFound', function ($message = 'Resource not found') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 404);
        });

        Response::macro('unauthorized', function ($message = 'Unauthorized') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 401);
        });

        Response::macro('forbidden', function ($message = 'Forbidden') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        });

        Response::macro('validationError', function ($errors, $message = 'Validation failed') {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], 422);
        });
    }
}
