<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            // Content
            $table->string('title');
            $table->string('slug', 600);
            $table->text('excerpt')->nullable();
            $table->longText('body');

            // Featured image — stored as JSON with all variants
            // {original, lg, md, sm, thumb, webp, alt, source, copyright}
            $table->json('featured_image')->nullable();

            // Status & Type (PHP Enums backed by DB ENUM)
            $table->enum('status', [
                'draft', 'pending_review', 'approved', 'published',
                'scheduled', 'archived', 'returned_for_revision', 'trashed'
            ])->default('draft');

            $table->enum('type', [
                'article', 'video', 'gallery', 'live', 'opinion', 'interview', 'analysis'
            ])->default('article');

            // Flags
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_exclusive')->default(false);
            $table->boolean('is_sticky')->default(false);
            $table->boolean('is_corrected')->default(false);
            $table->text('correction_note')->nullable();
            $table->boolean('allow_comments')->default(true);

            // Breaking news position (lower = higher priority)
            $table->integer('breaking_order')->nullable();

            // SEO
            $table->string('seo_title', 70)->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_og_image')->nullable();
            $table->json('schema_markup')->nullable();  // NewsArticle schema
            $table->string('canonical_url')->nullable();

            // Social
            $table->string('facebook_caption', 500)->nullable();
            $table->string('twitter_caption', 280)->nullable();

            // Stats (denormalized for speed)
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('share_count')->default(0);
            $table->unsignedInteger('reading_time_minutes')->nullable();

            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('embargoed_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes (critical for performance)
            $table->unique(['portal_id', 'slug']);
            $table->index(['portal_id', 'status', 'published_at']);
            $table->index(['portal_id', 'is_breaking', 'status']);
            $table->index(['portal_id', 'is_featured', 'status']);
            $table->index(['portal_id', 'category_id', 'status', 'published_at']);
            $table->index(['portal_id', 'scheduled_at', 'status']);
            $table->index(['author_id', 'portal_id', 'created_at']);
            $table->index('view_count');

            // Full-text search (MySQL FULLTEXT — fallback when MeiliSearch is down)
            // Note: Add these as raw statements in a separate migration after table creation
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('post_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('editor_id')->constrained('users')->restrictOnDelete();
            $table->json('old_content');   // {title, body, status, ...}
            $table->json('new_content');
            $table->string('change_summary')->nullable();
            $table->string('status_change')->nullable(); // "draft → pending_review"
            $table->ipAddress('editor_ip')->nullable();
            $table->timestamp('created_at');
            $table->index(['post_id', 'created_at']);
        });

        Schema::create('post_view_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('referer')->nullable();
            $table->string('device_type', 20)->nullable(); // mobile|tablet|desktop
            $table->timestamp('viewed_at');
            $table->index(['post_id', 'viewed_at']);
            $table->index(['portal_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_view_logs');
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('posts');
    }
};
