<?php

namespace Bhry98\LaravelApiResponse\Helpers;

enum ResponseKeys: string
{
    case Status = 'status';
    case Message = 'message';
    case Data = 'data';
    case Meta = 'meta';
    case Errors = 'errors';
}
