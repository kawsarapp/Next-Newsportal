<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('login_history')) {
            Schema::dropIfExists('login_history');
        }

        if (! Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_name')->nullable();
                $table->string('status', 50)->default('success');
                $table->string('failure_reason')->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['user_id', 'created_at']);
                $table->index(['ip_address', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
