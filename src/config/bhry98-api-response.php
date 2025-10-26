<?php

use Bhry98\LaravelApiResponse\Helpers\ResponseKeys;

return [
    // toggle trace inclusion even when APP_DEBUG is false (for dev/testing)
    'allow_trace' => env('API_RESPONSE_ALLOW_TRACE', false),
    // response keys/names
    'keys' => [
        ResponseKeys::Status->value => 'status',
        ResponseKeys::Message->value => 'message',
        ResponseKeys::Data->value => 'data',
        ResponseKeys::Meta->value => 'meta',
        ResponseKeys::Errors->value => 'errors',
    ],
    // default messages
    'messages' => [
        'success' => 'Operation successful',
        'error' => 'An error occurred',
        'validation' => 'Validation failed',
        'not_found' => 'Resource not found',
        'internal' => 'Internal server error',
    ],

    // whether to wrap responses in top-level "data" or not. If true, structure is {data: {...}}
    'wrap' => false,
];
