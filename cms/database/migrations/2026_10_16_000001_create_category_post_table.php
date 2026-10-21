<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('category_post')) {
            Schema::create('category_post', function (Blueprint $table) {
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->primary(['post_id', 'category_id']);
                $table->index(['category_id', 'post_id']);
            });
        }

        if (! Schema::hasColumn('posts', 'additional_category_ids')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('additional_category_ids')->nullable()->after('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post');

        if (Schema::hasColumn('posts', 'additional_category_ids')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('additional_category_ids');
            });
        }
    }
};
