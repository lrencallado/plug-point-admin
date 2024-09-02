<?php

namespace App\Enums\Api\V1;

enum ApiResponseCode : String
{
    case OK = 'OK';
    case BAD_REQUEST = 'BAD_REQUEST';
    case NOT_FOUND = 'NOT_FOUND';
    case SERVER_ERROR = 'SERVER_ERROR';
    case REQUEST_DENIED = 'REQUEST_DENIED';
    case PERMISSION_DENIED = 'PERMISSION_DENIED';
}
