<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');                 // e.g. "post.published"
            $table->string('subject_type')->nullable(); // e.g. "App\Models\Post"
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('context')->nullable();       // extra context data
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at');

            $table->index(['portal_id', 'action', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->ipAddress('ip_address');
            $table->string('user_agent', 500)->nullable();
            $table->string('device_name')->nullable();
            $table->enum('status', ['success', 'failed', 'locked'])->default('success');
            $table->string('failure_reason')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });

        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider');           // openai|gemini|local
            $table->string('model');
            $table->string('action_type');        // headline|summary|caption|grammar|seo|translate
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('cost_usd', 8, 6)->default(0);
            $table->boolean('response_cached')->default(false);
            $table->boolean('used_in_content')->default(false); // did editor keep the AI output?
            $table->timestamp('created_at');

            $table->index(['portal_id', 'created_at']);
            $table->index(['portal_id', 'action_type', 'created_at']);
        });

        Schema::create('reporter_payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('stories_count')->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['portal_id', 'reporter_id', 'year', 'month']);
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->json('events');            // ["post.published", "post.updated"]
            $table->string('secret', 64);      // HMAC signing secret
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('retry_count')->default(0);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['webhook_id', 'status', 'created_at']);
        });

        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('widget_key');
            $table->integer('position')->default(0);
            $table->integer('col_span')->default(1);  // grid columns (1-4)
            $table->json('config')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'portal_id', 'widget_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('reporter_payment_records');
        Schema::dropIfExists('ai_requests');
        Schema::dropIfExists('login_history');
        Schema::dropIfExists('audit_logs');
    }
};
