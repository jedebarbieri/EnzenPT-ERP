<?php

namespace App\Exceptions;

use App\Http\Controllers\ApiResponse;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }
        if ($e instanceof ModelNotFoundException) {
            return $this->modelNotFound($request, $e);
        }
        if ($e instanceof ValidationException) {
            return $this->validationError($request, $e);
        }
        $response = ApiResponse::error(
            message: $e->getMessage(),
            code: $e->getCode() ?: ApiResponse::HTTP_INTERNAL_SERVER_ERROR,
            originalException: $e
        );
        return $response->send();
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $response = ApiResponse::error(
            message: 'Unauthenticated or token expired',
            code: ApiResponse::HTTP_UNAUTHORIZED,
            originalException: $exception
        );
        return $response->send();
    }

    protected function modelNotFound($request, ModelNotFoundException $exception)
    {
        $response = ApiResponse::error(
            message: 'Resource not found',
            code: ApiResponse::HTTP_NOT_FOUND,
            originalException: $exception
        );
        return $response->send();
    }

    protected function validationError($request, ValidationException $exception)
    {
        $response = ApiResponse::error(
            message: 'Validation error',
            code: ApiResponse::HTTP_UNPROCESSABLE_ENTITY,
            originalException: $exception
        );
        return $response->send();
    }
}
