<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique()->nullable();

            // API Key (stored as prefix + hash, never plain)
            $table->string('api_key_prefix', 12)->unique();
            $table->string('api_key_hash', 64)->unique();
            $table->string('api_key_old_hash', 64)->nullable(); // grace period on rotation
            $table->timestamp('api_key_rotated_at')->nullable();

            // Identity
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('language', 10)->default('bn');
            $table->string('timezone')->default('Asia/Dhaka');

            // Plan & Status
            $table->enum('plan', ['free', 'basic', 'pro', 'enterprise'])->default('basic');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('trial_ends_at')->nullable();

            // Settings (overflow JSON for portal-level config)
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portals');
    }
};
