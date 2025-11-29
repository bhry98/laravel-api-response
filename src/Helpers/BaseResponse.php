<?php


namespace Bhry98\LaravelApiResponse\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Traits\Macroable;
use Throwable;

abstract class BaseResponse
{
    use Macroable;

    protected int $statusCode = 200;
    protected string $message = '';
    protected mixed $data = null;
    protected array $meta = [];
    protected array $additions = [];
    protected array $errors = [];
    protected ?array $trace = null;

    /** Create new instance and optionally set initial options */
    public static function make(array $options = []): static
    {
        $instance = new static();

        foreach ($options as $k => $v) {
            if (property_exists($instance, $k)) {
                $instance->{$k} = $v;
            } elseif (method_exists($instance, $k)) {
                // allow set via methods if exist, e.g. meta([...])
                $instance->{$k}($v);
            }
        }

        return $instance;
    }

    public function data(mixed $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function additions(array $extra): static
    {
        $this->additions = array_merge($this->additions, $extra);
        return $this;
    }

    public function meta(array $meta): static
    {
        $this->meta = array_merge($this->meta, $meta);
        return $this;
    }

    /**
     * Include exception trace and message if allowed.
     * You can pass a Throwable or Exception.
     */
    public function trace(Throwable $exception): static
    {
        $shouldAllow = config('api-response.allow_trace', false) || config('app.debug', env('APP_DEBUG', false));

        if (!$shouldAllow) {
            // do not include trace in production unless explicitly allowed
            return $this;
        }

        $this->trace = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile() . ':' . $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(fn($t) => [
                'file' => $t['file'] ?? null,
                'line' => $t['line'] ?? null,
                'function' => $t['function'] ?? null,
                'class' => $t['class'] ?? null,
            ])->all(),
        ];

        return $this;
    }

    /**
     * Pull message from translations using given message as translation key.
     * If message is empty, fall back to default defined in child classes or config.
     */
    public function localize(string $langKey = null): static
    {
        $key = $langKey ?: $this->message;
        $translated = __($key);

        // if translation found and not equal to the key, use it
        if (!empty($translated) && $translated !== $key) {
            $this->message = $translated;
        }

        return $this;
    }

    public function errors(array $errors): static
    {
        $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    public function statusCode(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function toArray(): array
    {
        $keys = config('api-response.keys');

        $payload = [
            $keys['status'] => $this->statusCode,
            $keys['message'] => $this->message,
            $keys['data'] => $this->data,
        ];

        if (!empty($this->meta)) {
            $payload[$keys['meta']] = $this->meta;
        }

        if (!empty($this->errors)) {
            $payload[$keys['errors']] = $this->errors;
        }

        if (!empty($this->additions)) {
            $payload = array_merge($payload, $this->additions);
        }

        if ($this->trace !== null) {
            $payload['trace'] = $this->trace;
        }

        if (config('api-response.wrap', false)) {
            return ['data' => $payload];
        }

        return $payload;
    }

    public function toJson(int $options = 0): JsonResponse
    {
        $configMessage = config('api-response.messages.' . $this->statusCode);

        return response()->json([
            'status' => $this->statusCode,
            'message' => $this->message ?? ($configMessage['default'] ?? ucfirst($this->statusCode)),
            'data' => $this->data ?? [],
            'additions' => $this->additions ?? [],
            'meta' => $this->meta ?? [],
        ],
            $this->statusCode
        );
    }

    /** alias for toJson() to keep your suggested naming */
    public function send(): JsonResponse
    {
        return $this->toJson();
    }
}
