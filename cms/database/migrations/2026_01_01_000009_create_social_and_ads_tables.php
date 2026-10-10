<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('platform');        // facebook|instagram|x|telegram|youtube
            $table->string('account_name');
            $table->string('account_id')->nullable();
            $table->text('access_token');      // encrypted
            $table->text('refresh_token')->nullable(); // encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'error'])->default('active');
            $table->json('meta')->nullable();   // platform-specific data
            $table->timestamps();

            $table->unique(['portal_id', 'platform', 'account_id']);
        });

        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_account_id')->constrained()->cascadeOnDelete();
            $table->string('platform');
            $table->text('caption')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'published', 'failed'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('platform_post_id')->nullable(); // ID returned by platform
            $table->text('error_message')->nullable();
            $table->json('performance')->nullable();  // {likes, shares, reach, ...}
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->index(['portal_id', 'status', 'scheduled_at']);
            $table->index(['post_id', 'platform']);
        });

        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('location');  // header|sidebar|in-content-1|in-content-2|footer
            $table->json('device_targets')->nullable(); // ['mobile', 'desktop']
            $table->json('page_targets')->nullable();   // ['homepage', 'article', 'category']
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('advertisers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advertiser_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('slot_id')->constrained('ad_slots')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['adsense', 'ad_manager', 'direct', 'sponsorship'])->default('direct');
            $table->text('code');           // HTML/JS ad code
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->json('performance')->nullable(); // {impressions, clicks, revenue}
            $table->timestamps();

            $table->index(['portal_id', 'status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
        Schema::dropIfExists('advertisers');
        Schema::dropIfExists('ad_slots');
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('social_accounts');
    }
};
