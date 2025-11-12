<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

class NotFoundResponse extends BaseResponse
{
    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.not_found', 'Resource not found');
        $this->statusCode = 404;
    }
}