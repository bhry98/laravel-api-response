<?php

namespace Bhry98\LaravelApiResponse\Responses;

use Bhry98\LaravelApiResponse\Helpers\BaseResponse;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class ValidationErrorResponse extends BaseResponse
{
    protected int $statusCode = 422;
    public function __construct()
    {
        $this->message = config('bhry98-api-response.messages.validation', 'Validation failed');
    }



    public function fromValidator($validator): static
    {
        if (method_exists($validator, 'errors')) {
            $errors = $validator->errors()->toArray();
        } elseif ($validator instanceof MessageBag || $validator instanceof ViewErrorBag) {
            $errors = $validator->toArray();
        } else {
            $errors = (array) $validator;
        }

        $this->errors($errors);
        $this->data(null);

        return $this;
    }
}