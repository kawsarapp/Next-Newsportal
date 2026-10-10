<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1)->index();
            $table->string('endpoint', 500)->unique();
            $table->text('public_key')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable(); // Chrome, Firefox, Safari
            $table->ipAddress('ip_address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('push_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1)->index();
            $table->unsignedBigInteger('user_id')->nullable(); // sender
            $table->string('title');
            $table->text('message');
            $table->string('target_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('segment')->default('all'); // all, desktop, mobile
            $table->integer('sent_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('status')->default('sent'); // draft, scheduled, sent
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_campaigns');
        Schema::dropIfExists('push_subscriptions');
    }
};
