<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Testing;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;

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

        // Resolves the JSON path of an envelope key, honouring both
        // config('...keys') renames and config('...wrap').
        $path = static function (string $key): string {
            $name = config("bhry98-api-response.keys.{$key}", $key);

            return config('bhry98-api-response.wrap', false) ? "data.{$name}" : $name;
        };

        TestResponse::macro('assertApiSuccess', function (?string $message = null) use ($path) {
            /** @var TestResponse $this */
            $this->assertJsonPath($path('status'), true);

            if ($message !== null) {
                $this->assertJsonPath($path('message'), $message);
            }

            return $this;
        });

        TestResponse::macro('assertApiError', function (?string $message = null) use ($path) {
            /** @var TestResponse $this */
            $this->assertJsonPath($path('status'), false);

            if ($message !== null) {
                $this->assertJsonPath($path('message'), $message);
            }

            return $this;
        });

        TestResponse::macro('assertApiValidation', function (array $fields = []) use ($path) {
            /** @var TestResponse $this */
            $errorsPath = $path('errors');
            $errors = $this->json($errorsPath);

            Assert::assertIsArray($errors, "Response is missing the [{$errorsPath}] key.");

            foreach ($fields as $field) {
                Assert::assertArrayHasKey($field, $errors, "No validation error for [{$field}] in [{$errorsPath}].");
            }

            return $this;
        });

        TestResponse::macro('assertApiMessage', function (string $message) use ($path) {
            /** @var TestResponse $this */
            return $this->assertJsonPath($path('message'), $message);
        });

        TestResponse::macro('assertApiStatus', function (bool $status) use ($path) {
            /** @var TestResponse $this */
            return $this->assertJsonPath($path('status'), $status);
        });

        TestResponse::macro('assertApiData', function (array $data) use ($path) {
            /** @var TestResponse $this */
            return $this->assertJsonPath($path('data'), $data);
        });
    }
}
