<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Check if the exception is an instance of ModelNotFoundException
        // This exception is thrown when a model (e.g., Employee) is not found by the specified ID or criteria
        if ($exception instanceof ModelNotFoundException) {
            // Return a JSON response with a 404 status code and a custom error message
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found' // Custom error message for not found resources
            ], Response::HTTP_NOT_FOUND);
        }

        // For all other exceptions, call the parent class's render method to handle them normally
        return parent::render($request, $exception);
    }
}
