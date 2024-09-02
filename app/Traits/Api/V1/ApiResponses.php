<?php

namespace App\Traits\Api\V1;

use App\Enums\Api\V1\ApiResponseCode;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function badRequest($message, $errors = []): JsonResponse
    {
        return $this->error($message, $errors, ApiResponseCode::BAD_REQUEST, 400);
    }

    protected function notFound($message, $errors = []): JsonResponse
    {
        return $this->error($message, $errors, ApiResponseCode::NOT_FOUND, 404);
    }

    protected function internalServerError($message, $errors = []): JsonResponse
    {
        return $this->error($message, $errors, ApiResponseCode::SERVER_ERROR, 500);
    }

    protected function externalPermissionDenied($message, $errors = []): JsonResponse
    {
        return $this->error($message, $errors, ApiResponseCode::PERMISSION_DENIED, 403);
    }

    /**
     * Return a success response with a message and a status.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function success($message, $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'status' => ApiResponseCode::OK,
        ], $statusCode);
    }

    /**
     * Return a response with a resource.
     *
     * @param mixed $resource
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function resource($resource, $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $resource,
            'status' => ApiResponseCode::OK,
        ], $statusCode);
    }

   /**
    * Return an error response
    *
    * @param string $message
    * @param array $errors
    * @param string $status
    * @param integer $statusCode
    * @return JsonResponse
    */
    protected function error($message, $errors = [], $status = ApiResponseCode::BAD_REQUEST, $statusCode = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'status' => $status,
        ], $statusCode);
    }
}
