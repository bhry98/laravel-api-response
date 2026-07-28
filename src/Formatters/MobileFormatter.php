<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Formatters;

use Bhry98\LaravelApiResponse\Data\ResponsePayload;

/**
 * Same shape as the default envelope but recursively strips null values to
 * shave payload size for bandwidth-constrained mobile clients.
 */
class MobileFormatter extends DefaultFormatter
{
    public function format(ResponsePayload $payload): array
    {
        return $this->stripNulls(parent::format($payload));
    }

    private function stripNulls(array $value): array
    {
        $result = [];

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->stripNulls($item);

                continue;
            }

            if ($item === null) {
                continue;
            }

            $result[$key] = $item;
        }

        return $result;
    }
}
