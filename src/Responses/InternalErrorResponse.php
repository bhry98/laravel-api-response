<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;

/**
 * @deprecated Use api()->internal() instead.
 */
class InternalErrorResponse extends BaseResponse
{
    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.internal', 'Internal server error');
        $this->statusCode = 500;
    }
}
