<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

class SuccessResponse extends BaseResponse
{
    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.success', 'Operation successful');
        $this->statusCode = 200;
    }
}