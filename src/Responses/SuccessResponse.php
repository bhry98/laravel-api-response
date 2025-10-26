<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

class SuccessResponse extends BaseResponse
{
    protected int $statusCode = 200;
    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.success', 'Operation successful');
    }
}