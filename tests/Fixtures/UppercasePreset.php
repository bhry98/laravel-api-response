<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Tests\Fixtures;

use Bhry98\LaravelApiResponse\Contracts\PresetContract;
use Bhry98\LaravelApiResponse\Contracts\ResponseFormatterContract;
use Bhry98\LaravelApiResponse\Data\ResponsePayload;

class UppercasePreset implements PresetContract, ResponseFormatterContract
{
    public function formatter(): ResponseFormatterContract
    {
        return $this;
    }

    public function format(ResponsePayload $payload): array
    {
        return [
            'STATUS' => $payload->success,
            'MESSAGE' => strtoupper($payload->message),
        ];
    }
}
