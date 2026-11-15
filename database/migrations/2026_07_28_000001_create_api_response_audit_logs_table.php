<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('bhry98-api-response.audit_log.connection'))
            ->create(config('bhry98-api-response.audit_log.table', 'api_response_audit_logs'), function (Blueprint $table) {
                $table->id();
                $table->string('method', 10);
                $table->text('url');
                $table->string('route_name')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                // string, so integer, UUID and ULID user keys all fit.
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

                $table->index('user_id');
                $table->index('route_name');
                $table->index('created_at');
            });
    }

    public function down(): void
    {
        Schema::connection(config('bhry98-api-response.audit_log.connection'))
            ->dropIfExists(config('bhry98-api-response.audit_log.table', 'api_response_audit_logs'));
    }
};
