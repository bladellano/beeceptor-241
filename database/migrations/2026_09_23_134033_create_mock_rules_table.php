<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mock_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('endpoint_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('priority')->default(1);
            $table->string('method', 16);
            $table->string('path_pattern');
            $table->string('path_match_type', 32)->default('exact');
            $table->json('query_conditions')->nullable();
            $table->json('header_conditions')->nullable();
            $table->json('body_conditions')->nullable();
            $table->unsignedSmallInteger('response_status')->default(200);
            $table->json('response_headers')->nullable();
            $table->longText('response_body')->nullable();
            $table->unsignedInteger('response_delay')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['endpoint_id', 'priority']);
            $table->index(['endpoint_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mock_rules');
    }
};
