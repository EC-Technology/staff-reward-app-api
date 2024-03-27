<?php

namespace App\Exceptions;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use App\Traits\ApiResponser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            return $this->errorResponse('Unauthorized', StatusCode::HTTP_UNAUTHORIZED, ErrorCode::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('The specific method for the request is invalid', StatusCode::HTTP_METHOD_NOT_ALLOWED);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->errorResponse('The specific URL cannot be found', StatusCode::HTTP_NOT_FOUND);
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        if ($e instanceof ValidationException) {
            return $this->errorResponse($e->getMessage(), StatusCode::HTTP_UNPROCESSABLE_CONTENT, ErrorCode::REQUEST_PARAMETER_MISSING_OR_INCORRECT);
        }

        if ($e instanceof CampaignQuotaException) {
            return $this->errorResponse($e->getMessage(), StatusCode::HTTP_BAD_REQUEST, ErrorCode::CAMPAIGN_QUOTA_FULL);
        }

        if ($e instanceof MerchantApiException) {
            return $this->errorResponse($e->getMessage(), StatusCode::HTTP_INTERNAL_SERVER_ERROR, ErrorCode::CRM_MERCHANT_API_ERROR);
        }

        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return $this->errorResponse('Unexpected Exception. Try later', StatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }
}
