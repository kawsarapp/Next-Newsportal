<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subeditor_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1);
            $table->unsignedBigInteger('post_id')->nullable();
            $table->string('title');
            $table->string('category_name')->nullable();
            $table->string('status')->default('published'); // published, draft, failed
            $table->string('url')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subeditor_logs');
    }
};
