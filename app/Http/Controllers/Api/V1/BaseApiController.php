<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Api\V1\ApiResponseCode;
use App\Http\Controllers\Controller;
use App\Traits\Api\V1\ApiResponses;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    use ApiResponses;

    protected function handleGoogleApiResponse($data)
    {
        if (isset($data['error'])) {
            if ($data['error'] === 'INVALID_REQUEST') {
                return $this->badRequest($data['error_message']);
            }

            if ($data['error'] === 'REQUEST_DENIED') {
                return $this->error($data['error_message'], [], ApiResponseCode::REQUEST_DENIED);
            }

        }
        return $this->resource($data);
    }
}
