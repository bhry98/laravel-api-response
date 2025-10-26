<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

class InternalErrorResponse extends BaseResponse
{
    protected int $statusCode = 500;

    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.internal', 'Internal server error');
    }
}