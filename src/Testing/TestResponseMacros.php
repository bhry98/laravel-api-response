<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Testing;

use Illuminate\Testing\TestResponse;

/**
 * Registers assertApiSuccess()/assertApiError()/... on Illuminate\Testing\TestResponse
 * so they work in PHPUnit and Pest without any extra trait/import.
 */
class TestResponseMacros
{
    public static function register(): void
    {
        if (! class_exists(TestResponse::class)) {
            return;
        }

        TestResponse::macro('assertApiSuccess', function (?string $message = null) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);
            $this->assertJsonPath($keys['status'] ?? 'status', true);

            if ($message !== null) {
                $this->assertJsonPath($keys['message'] ?? 'message', $message);
            }

            return $this;
        });

        TestResponse::macro('assertApiError', function (?string $message = null) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);
            $this->assertJsonPath($keys['status'] ?? 'status', false);

            if ($message !== null) {
                $this->assertJsonPath($keys['message'] ?? 'message', $message);
            }

            return $this;
        });

        TestResponse::macro('assertApiValidation', function (array $fields = []) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);
            $errorsKey = $keys['errors'] ?? 'errors';

            $this->assertJsonStructure([$errorsKey]);

            foreach ($fields as $field) {
                $this->assertJsonStructure([$errorsKey => [$field]]);
            }

            return $this;
        });

        TestResponse::macro('assertApiMessage', function (string $message) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);

            return $this->assertJsonPath($keys['message'] ?? 'message', $message);
        });

        TestResponse::macro('assertApiStatus', function (bool $status) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);

            return $this->assertJsonPath($keys['status'] ?? 'status', $status);
        });

        TestResponse::macro('assertApiData', function (array $data) {
            /** @var TestResponse $this */
            $keys = config('bhry98-api-response.keys', []);

            return $this->assertJsonPath($keys['data'] ?? 'data', $data);
        });
    }
}
