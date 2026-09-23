<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->cascadeOnDelete();
            $table->string('method', 16);
            $table->text('url');
            $table->string('path', 2048);
            $table->json('query_parameters')->nullable();
            $table->json('headers')->nullable();
            $table->longText('body')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('matched_rule_id')->nullable()->constrained('mock_rules')->nullOnDelete();
            $table->boolean('fallback_used')->default(false);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('endpoint_id');
            $table->index('created_at');
            $table->index('method');
            $table->index('response_status');
            $table->index('path');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
