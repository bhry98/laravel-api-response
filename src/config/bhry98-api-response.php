<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Presets\DefaultPreset;
use Bhry98\LaravelApiResponse\Presets\JsonApiPreset;
use Bhry98\LaravelApiResponse\Presets\MinimalPreset;
use Bhry98\LaravelApiResponse\Presets\MobilePreset;
use Bhry98\LaravelApiResponse\Support\MetaProviders\ApiVersionMetaProvider;
use Bhry98\LaravelApiResponse\Support\MetaProviders\ExecutionTimeMetaProvider;
use Bhry98\LaravelApiResponse\Support\MetaProviders\LocaleMetaProvider;
use Bhry98\LaravelApiResponse\Support\MetaProviders\RequestIdMetaProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return [

    /*
    |--------------------------------------------------------------------------
    | Response Preset
    |--------------------------------------------------------------------------
    |
    | The default preset used to shape every response envelope. Built-in
    | presets: "default", "minimal", "mobile", "jsonapi". Override per
    | response with api()->preset('minimal'), or register your own preset
    | class below / at runtime via the PresetManager.
    |
    */
    'preset' => env('API_RESPONSE_PRESET', 'default'),

    'presets' => [
        'default' => DefaultPreset::class,
        'minimal' => MinimalPreset::class,
        'mobile' => MobilePreset::class,
        'jsonapi' => JsonApiPreset::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Keys
    |--------------------------------------------------------------------------
    |
    | Rename any top-level key in the generated JSON envelope without
    | touching a single controller. Only affects the "default"/"minimal"/
    | "mobile" formatters - "jsonapi" has its own fixed shape.
    |
    */
    'keys' => [
        'status' => 'status',
        'message' => 'message',
        'data' => 'data',
        'errors' => 'errors',
        'meta' => 'meta',
        'links' => 'links',
    ],

    /*
    |--------------------------------------------------------------------------
    | Wrap Envelope
    |--------------------------------------------------------------------------
    |
    | When true, the whole envelope is nested one level deeper:
    | {"data": {"status": true, "message": "...", "data": {...}}}
    |
    */
    'wrap' => env('API_RESPONSE_WRAP', false),

    /*
    |--------------------------------------------------------------------------
    | Default Messages
    |--------------------------------------------------------------------------
    |
    | Fallback messages used by the ready-made HTTP and business response
    | shortcuts (api()->ok(), api()->updated(), ...) when no explicit
    | message is passed. These are treated as translation keys when a
    | matching entry exists in lang/vendor/bhry98-api-response/{locale}/api.php,
    | so you get localization for free - see api()->localize().
    |
    */
    'messages' => [
        // http
        'success' => 'Operation successful.',
        'created' => 'Resource created successfully.',
        'accepted' => 'Request accepted.',
        'error' => 'An error occurred.',
        'validation' => 'The given data was invalid.',
        'not_found' => 'Resource not found.',
        'internal' => 'Internal server error.',
        'unauthorized' => 'Unauthenticated.',
        'forbidden' => 'This action is unauthorized.',
        'bad_request' => 'Bad request.',
        'conflict' => 'Conflict.',
        'too_many_requests' => 'Too many requests.',
        'service_unavailable' => 'Service unavailable.',

        // business
        'saved' => 'Saved successfully.',
        'updated' => 'Updated successfully.',
        'deleted' => 'Deleted successfully.',
        'restored' => 'Restored successfully.',
        'archived' => 'Archived successfully.',
        'verified' => 'Verified successfully.',
        'registered' => 'Registered successfully.',
        'logged_in' => 'Logged in successfully.',
        'logged_out' => 'Logged out successfully.',
        'password_changed' => 'Password changed successfully.',
        'uploaded' => 'Uploaded successfully.',
        'downloaded' => 'Download ready.',
        'email_sent' => 'Email sent successfully.',
        'otp_sent' => 'OTP sent successfully.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Templates
    |--------------------------------------------------------------------------
    |
    | Reusable, named response definitions. Usage: api()->template('user_created').
    |
    | 'user_created' => [
    |     'status' => true,
    |     'message' => 'User created successfully.',
    |     'code' => 201,
    | ],
    |
    */
    'responses' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Metadata
    |--------------------------------------------------------------------------
    |
    | Keys listed in "enabled" are automatically collected and merged into
    | every response's `meta` block. Add your own provider by implementing
    | MetaProviderContract and registering it here.
    |
    */
    'meta' => [
        'enabled' => [
            // 'request_id',
            // 'execution_time',
            // 'api_version',
            // 'locale',
        ],

        'providers' => [
            'request_id' => RequestIdMetaProvider::class,
            'execution_time' => ExecutionTimeMetaProvider::class,
            'api_version' => ApiVersionMetaProvider::class,
            'locale' => LocaleMetaProvider::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Exposed via the "api_version" meta provider and overridable per
    | response with api()->version('v2').
    |
    */
    'version' => env('API_RESPONSE_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | When enabled (and app.debug is also true - both are required so this
    | can never leak into production by accident), a `debug` block with the
    | selected diagnostics is attached to every response.
    |
    */
    'debug' => [
        'enabled' => env('API_RESPONSE_DEBUG', false),
        'include' => ['queries', 'time', 'memory', 'exception', 'trace'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Trace (legacy ->trace())
    |--------------------------------------------------------------------------
    |
    | Allows api()->trace($exception) to attach exception details even when
    | app.debug is false. Prefer the "debug" block above for new code.
    |
    */
    'allow_trace' => env('API_RESPONSE_ALLOW_TRACE', false),

    /*
    |--------------------------------------------------------------------------
    | Exception Handling
    |--------------------------------------------------------------------------
    |
    | When enabled, the package's exception renderer takes over formatting
    | for JSON/"expects JSON" requests and turns the listed exception types
    | into api() responses automatically. Fully opt-in.
    |
    */
    'exceptions' => [
        'override_exception_handler' => env('API_RESPONSE_OVERRIDE_EXCEPTIONS', false),

        'map' => [
            ValidationException::class => 422,
            AuthenticationException::class => 401,
            AuthorizationException::class => 403,
            ModelNotFoundException::class => 404,
            HttpExceptionInterface::class => null, // uses the exception's own status code
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Override Default response()->json()
    |--------------------------------------------------------------------------
    |
    | When enabled, response()->json($data) is macro'd to automatically wrap
    | $data in the package's standard envelope. Fully opt-in - never forced.
    |
    */
    'override_default_response' => env('API_RESPONSE_OVERRIDE_DEFAULT', false),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Opt-in throttling for any route/group via the "api.rate_limit"
    | middleware alias. Uses Laravel's own RateLimiter under the hood but
    | returns a standard api()->tooManyRequests() envelope (with
    | Retry-After/X-RateLimit-* headers) instead of the framework's default
    | ThrottleRequestsException response.
    |
    | 'by' controls how requests are bucketed: "ip", "user" (falls back to
    | ip for guests) or "route". Override max_attempts/decay_seconds per
    | route with middleware parameters: ->middleware('api.rate_limit:100,1')
    |
    */
    'rate_limit' => [
        'enabled' => env('API_RESPONSE_RATE_LIMIT_ENABLED', false),
        'max_attempts' => env('API_RESPONSE_RATE_LIMIT_MAX_ATTEMPTS', 60),
        'decay_seconds' => env('API_RESPONSE_RATE_LIMIT_DECAY_SECONDS', 60),
        'by' => env('API_RESPONSE_RATE_LIMIT_BY', 'ip'),
        'key_prefix' => env('API_RESPONSE_RATE_LIMIT_PREFIX', 'api-response-rate-limit'),
        'message' => null, // null falls back to messages.too_many_requests
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Log
    |--------------------------------------------------------------------------
    |
    | Opt-in request/response auditing via the "api.audit" middleware alias.
    | Entries are written to "table" (see the publishable migration) through
    | a queued job by default so logging never slows down the response -
    | set queue.enabled to false to write synchronously instead (handy for
    | local/testing). queue.connection/queue.queue map directly onto
    | Bhry98\LaravelApiResponse\Jobs\StoreAuditLogEntry::onConnection()/
    | onQueue() - leave them null to use your app's default queue connection
    | (config('queue.default'), e.g. "sync", "database", "redis", ...).
    |
    | Anything under "hidden" is redacted (replaced with "***") wherever it
    | appears as a request/response body key before the entry is persisted.
    |
    */
    'audit_log' => [
        'enabled' => env('API_RESPONSE_AUDIT_LOG_ENABLED', false),

        'connection' => env('API_RESPONSE_AUDIT_LOG_DB_CONNECTION'), // null = default DB connection
        'table' => env('API_RESPONSE_AUDIT_LOG_TABLE', 'api_response_audit_logs'),

        'queue' => [
            'enabled' => env('API_RESPONSE_AUDIT_LOG_QUEUE_ENABLED', true),
            'connection' => env('API_RESPONSE_AUDIT_LOG_QUEUE_CONNECTION'), // null = config('queue.default')
            'queue' => env('API_RESPONSE_AUDIT_LOG_QUEUE_NAME'), // null = the connection's default queue
        ],

        'capture' => [
            'request_body' => true,
            'response_body' => true,
            'headers' => false,
        ],

        'methods' => [], // e.g. ['POST', 'PUT', 'PATCH', 'DELETE'] - empty means every method
        'exclude_routes' => [], // route names or URI patterns (fnmatch), e.g. 'telescope*'

        'hidden' => [
            'password', 'password_confirmation', 'token', 'secret',
            'authorization', 'api_key', 'access_token', 'refresh_token', 'card_number', 'cvv',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Resolution
    |--------------------------------------------------------------------------
    |
    | Opt-in per-request locale resolution via the "api.locale" middleware
    | alias. Priority order:
    |   1. The "Accept-Language" request header (first tag, matched against
    |      "supported" when that list is non-empty).
    |   2. The authenticated user's locale column - "user_column" names it,
    |      so this works whatever your users table calls it ("locale",
    |      "lang", "preferred_language", ...).
    |   3. "fallback" (defaults to config('app.locale')).
    |
    */
    'locale' => [
        'enabled' => env('API_RESPONSE_LOCALE_ENABLED', false),
        'supported' => [], // e.g. ['en', 'ar', 'fr'] - empty means accept any tag from the header
        'user_column' => env('API_RESPONSE_LOCALE_USER_COLUMN', 'locale'),
        'fallback' => env('API_RESPONSE_LOCALE_FALLBACK', config('app.locale', 'en')),
    ],

];
