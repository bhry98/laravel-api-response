<?php

declare(strict_types=1);

use Bhry98\LaravelApiResponse\Jobs\StoreAuditLogEntry;
use Bhry98\LaravelApiResponse\Models\AuditLog;
use Bhry98\LaravelApiResponse\Tests\Fixtures\AuthenticatableUserStub;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('api_response_audit_logs', function (Blueprint $table) {
        $table->id();
        $table->string('method', 10);
        $table->text('url');
        $table->string('route_name')->nullable();
        $table->string('ip_address', 45)->nullable();
        $table->string('user_agent')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
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

    Route::middleware('api.audit')->post('/audit-me', fn () => api()->created(['id' => 1]));
});

it('does nothing when disabled', function () {
    config(['bhry98-api-response.audit_log.enabled' => false]);

    $this->postJson('/audit-me', ['password' => 'secret'])->assertStatus(201);

    expect(AuditLog::count())->toBe(0);
});

it('queues a StoreAuditLogEntry job by default instead of writing synchronously', function () {
    Queue::fake();

    config([
        'bhry98-api-response.audit_log.enabled' => true,
        'bhry98-api-response.audit_log.queue.enabled' => true,
    ]);

    $this->postJson('/audit-me', ['password' => 'secret'])->assertStatus(201);

    Queue::assertPushed(StoreAuditLogEntry::class);
    expect(AuditLog::count())->toBe(0);
});

it('writes synchronously when queueing is disabled and redacts hidden fields', function () {
    config([
        'bhry98-api-response.audit_log.enabled' => true,
        'bhry98-api-response.audit_log.queue.enabled' => false,
        'bhry98-api-response.audit_log.hidden' => ['password'],
    ]);

    $user = new AuthenticatableUserStub(['id' => 42]);
    $this->be($user);

    $this->postJson('/audit-me', ['email' => 'a@b.com', 'password' => 'secret'])->assertStatus(201);

    $entry = AuditLog::query()->firstOrFail();

    expect($entry->method)->toBe('POST');
    expect($entry->response_status)->toBe(201);
    expect($entry->user_type)->toBe(AuthenticatableUserStub::class);
    expect($entry->request_body['email'])->toBe('a@b.com');
    expect($entry->request_body['password'])->toBe('***');
    expect($entry->response_body)->toBe(['status' => true, 'message' => 'Resource created successfully.', 'data' => ['id' => 1]]);
});

it('skips excluded routes', function () {
    config([
        'bhry98-api-response.audit_log.enabled' => true,
        'bhry98-api-response.audit_log.queue.enabled' => false,
        'bhry98-api-response.audit_log.exclude_routes' => ['audit-me'],
    ]);

    $this->postJson('/audit-me', [])->assertStatus(201);

    expect(AuditLog::count())->toBe(0);
});
