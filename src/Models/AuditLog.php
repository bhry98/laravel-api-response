<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single recorded request/response, written by
 * Bhry98\LaravelApiResponse\Middleware\AuditRequests via the queued
 * StoreAuditLogEntry job. Table/connection are resolved dynamically from
 * config('bhry98-api-response.audit_log') so a custom table name/connection
 * "just works" without touching this class.
 */
class AuditLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_body' => 'array',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('bhry98-api-response.audit_log.table', 'api_response_audit_logs');
    }

    public function getConnectionName(): ?string
    {
        return config('bhry98-api-response.audit_log.connection');
    }
}
