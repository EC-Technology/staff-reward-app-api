<?php

namespace App\Traits;

use App\Enum\ErrorCode;
use App\Enum\StatusCode;
use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    protected function successResponse($data = null, $message = null, $code = StatusCode::HTTP_OK, $count = null): JsonResponse
    {

        $resData = [
            'status' => 1,
            'message' => $message,
            'data' => $data,
            'error_code' => null
        ];

        if (!is_null($count)) {
            $resData['count'] = $count;
        }
        return response()->json($resData, $code);
    }

    protected function errorResponse($message = null, $code = StatusCode::HTTP_BAD_REQUEST, $errorCode = ErrorCode::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => null,
            'error_code' => $errorCode
        ], $code);
    }

    protected function configError($message = null) {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => null,
            'error_code' => null
        ], StatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function objectNotExist($message = null) {
        return response()->json([
            'status' => 0,
            'message' => $message,
            'data' => null,
            'error_code' => ErrorCode::OBJECT_NOT_EXIST
        ], StatusCode::HTTP_BAD_REQUEST);
    }
}
