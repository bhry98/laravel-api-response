<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Facades\BResponseSuccess;
use Bhry98\LaravelApiResponse\Models\AuditLog;
use Bhry98\LaravelApiResponse\Tests\Fixtures\UserStub;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('does not double-wrap api() responses when response()->json() is overridden', function () {
    config(['bhry98-api-response.override_default_response' => true]);

    expect(api()->ok(['id' => 1], 'Fine.')->send()->getData(true))
        ->toBe(['status' => true, 'message' => 'Fine.', 'data' => ['id' => 1]]);
});

it('does not double-wrap legacy responses when response()->json() is overridden', function () {
    config(['bhry98-api-response.override_default_response' => true]);

    $data = BResponseSuccess::make()->message('ok')->data(['a' => 1])->send()->getData(true);

    expect($data)->toBe(['status' => true, 'message' => 'ok', 'data' => ['a' => 1]]);
});

it('ignores translation keys that resolve to a whole group in localize()', function () {
    app('translator')->addLines(['grp.a' => 'A', 'grp.b' => 'B'], 'en');

    expect(api()->message('Keep me')->localize('grp')->build()['message'])->toBe('Keep me');
});

it('keeps ->trace() details when the global debug block is enabled', function () {
    config([
        'app.debug' => true,
        'bhry98-api-response.debug.enabled' => true,
        'bhry98-api-response.debug.include' => ['memory'],
    ]);

    $body = api()->fail()->trace(new RuntimeException('kaboom'))->build();

    expect($body['debug'])->toHaveKeys(['memory_usage_mb', 'exception'])
        ->and($body['debug']['exception']['message'])->toBe('kaboom');
});

describe('exception rendering', function () {
    beforeEach(fn () => config(['bhry98-api-response.exceptions.override_exception_handler' => true]));

    it('renders ModelNotFoundException as 404 without leaking the model name', function () {
        Route::get('/mnf', fn () => throw (new ModelNotFoundException)->setModel(UserStub::class, [5]));

        $response = $this->getJson('/mnf')->assertStatus(404)->assertJsonPath('status', false);

        expect($response->json('message'))->toBe('Resource not found.')
            ->and($response->getContent())->not->toContain('UserStub');
    });

    it('renders AuthorizationException with its own message and status', function () {
        Route::get('/deny', fn () => throw new AuthorizationException('Nope.'));
        Route::get('/deny-404', fn () => throw (new AuthorizationException('Hidden.'))->withStatus(404));

        $this->getJson('/deny')->assertStatus(403)->assertJsonPath('message', 'Nope.');
        $this->getJson('/deny-404')->assertStatus(404)->assertJsonPath('status', false);
    });

    it('keeps the headers of HTTP exceptions', function () {
        Route::get('/throttled', fn () => throw new HttpException(429, 'Slow down.', null, ['Retry-After' => 30]));

        $this->getJson('/throttled')
            ->assertStatus(429)
            ->assertHeader('Retry-After', '30')
            ->assertJsonPath('message', 'Slow down.');
    });

    it('honours custom classes in exceptions.map', function () {
        config(['bhry98-api-response.exceptions.map' => [
            ...config('bhry98-api-response.exceptions.map'),
            DomainException::class => 409,
        ]]);

        Route::get('/domain', fn () => throw new DomainException('Already exists.'));

        $this->getJson('/domain')
            ->assertStatus(409)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Already exists.');
    });
});

describe('audit log', function () {
    beforeEach(function () {
        Schema::create('api_response_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->text('url');
            $table->string('route_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('user_id', 64)->nullable();
            $table->string('user_type')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->unsignedSmallInteger('response_status');
            $table->json('response_body')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->string('locale', 10)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        config([
            'bhry98-api-response.audit_log.enabled' => true,
            'bhry98-api-response.audit_log.queue.enabled' => false,
        ]);

        Route::middleware('api.audit')->post('/audit-upload', fn () => api()->uploaded());
    });

    it('redacts hidden request headers such as Authorization', function () {
        config(['bhry98-api-response.audit_log.capture.headers' => true]);

        $this->postJson('/audit-upload', [], ['Authorization' => 'Bearer top-secret'])->assertStatus(201);

        $entry = AuditLog::query()->firstOrFail();

        expect($entry->request_headers['authorization'])->toBe('***')
            ->and(json_encode($entry->request_headers))->not->toContain('top-secret');
    });

    it('describes uploaded files instead of trying to store them', function () {
        $this->post('/audit-upload', ['avatar' => UploadedFile::fake()->create('me.png', 1)], ['Accept' => 'application/json'])
            ->assertStatus(201);

        expect(AuditLog::query()->firstOrFail()->request_body['avatar']['file'])->toBe('me.png');
    });

    it('matches configured methods case-insensitively', function () {
        config(['bhry98-api-response.audit_log.methods' => ['post']]);

        $this->postJson('/audit-upload')->assertStatus(201);

        expect(AuditLog::count())->toBe(1);
    });
});

it('ignores Accept-Language tags with q=0', function () {
    config(['bhry98-api-response.locale.enabled' => true, 'bhry98-api-response.locale.fallback' => 'en']);

    Route::middleware('api.locale')->get('/locale-q0', fn () => api()->ok(['locale' => app()->getLocale()]));

    $this->getJson('/locale-q0', ['Accept-Language' => 'fr;q=0, de;q=0.5'])
        ->assertJsonPath('data.locale', 'de');
});

it('lets the testing macros follow the wrap option', function () {
    config(['bhry98-api-response.wrap' => true]);

    Route::get('/wrapped', fn () => api()->unprocessable(['email' => ['Required.']]));

    $this->getJson('/wrapped')->assertApiError()->assertApiValidation(['email']);
});
