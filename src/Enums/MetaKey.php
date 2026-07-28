<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Enums;

enum MetaKey: string
{
    case RequestId = 'request_id';
    case ExecutionTime = 'execution_time';
    case ApiVersion = 'api_version';
    case Locale = 'locale';
}
