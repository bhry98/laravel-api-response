<?php

declare(strict_types=1);

namespace Bhry98\LaravelApiResponse\Jobs;

use Bhry98\LaravelApiResponse\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Persists a single audit log entry. Queued by default (see
 * config('bhry98-api-response.audit_log.queue')) so writing the log never
 * adds latency to the actual API response; AuditRequests dispatches this
 * synchronously instead when queue.enabled is false.
 */
class StoreAuditLogEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $entry
     */
    public function __construct(
        public readonly array $entry,
    ) {}

    public function handle(): void
    {
        AuditLog::query()->create($this->entry);
    }
}
