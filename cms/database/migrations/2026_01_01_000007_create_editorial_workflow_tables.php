<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editorial_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('current_stage'); // draft|pending_review|fact_check|copy_edit|legal|approved
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deadline')->nullable();
            $table->boolean('is_embargoed')->default(false);
            $table->timestamp('embargo_until')->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('requires_legal')->default(false);
            $table->boolean('requires_fact_check')->default(false);
            $table->timestamps();

            $table->unique('post_id'); // one workflow per post
            $table->index(['portal_id', 'current_stage', 'assigned_to']);
            $table->index(['portal_id', 'deadline']);
        });

        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('editorial_workflows')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('from_stage');
            $table->string('to_stage');
            $table->foreignId('by_user_id')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('created_at');
            $table->index(['workflow_id', 'created_at']);
        });

        Schema::create('editorial_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->boolean('is_private')->default(true); // private to editors, not reporter
            $table->timestamps();
            $table->index(['post_id', 'created_at']);
        });

        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('brief')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->timestamp('deadline')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'completed', 'cancelled'])
                  ->default('pending');
            $table->timestamps();
            $table->index(['reporter_id', 'status', 'deadline']);
            $table->index(['portal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('editorial_notes');
        Schema::dropIfExists('workflow_transitions');
        Schema::dropIfExists('editorial_workflows');
    }
};
