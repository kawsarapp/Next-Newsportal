<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Multi-portal: null = super admin (access all portals)
            $table->foreignId('portal_id')->nullable()->constrained('portals')->nullOnDelete()->after('id');

            $table->string('username')->unique()->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable();

            // Two-Factor Auth
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

            // Security
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            // Reporter info (nullable — only for reporter role)
            $table->string('reporter_type')->nullable();
            $table->unsignedBigInteger('district_id')->nullable(); // FK added after districts migration
            $table->string('institution')->nullable(); // university correspondents


            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'portal_id', 'username', 'phone', 'avatar',
                'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
                'status', 'login_attempts', 'locked_until', 'last_login_at', 'last_login_ip',
                'reporter_type', 'district_id', 'institution', 'deleted_at',
            ]);
        });
    }
};
