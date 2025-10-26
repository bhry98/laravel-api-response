<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

class ErrorResponse extends BaseResponse
{
    protected int $statusCode = 400;


    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.error', 'An error occurred');
    }
}