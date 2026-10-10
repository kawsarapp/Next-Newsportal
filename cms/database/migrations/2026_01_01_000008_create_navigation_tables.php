<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('location'); // header|footer|sidebar|mobile
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['portal_id', 'location']);
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('custom'); // custom|category|tag|page|post
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('url')->nullable();
            $table->string('target', 10)->default('_self'); // _self|_blank
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'parent_id', 'order']);
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();         // section heading
            $table->string('section_type');              // hero|grid|list|ticker|featured|ad
            $table->string('layout_type')->default('grid'); // grid|list|slider|masonry
            $table->integer('posts_count')->default(6);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();          // extra per-section config
            $table->timestamps();

            $table->index(['portal_id', 'order', 'is_active']);
        });

        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('body');
            $table->string('template')->default('default'); // default|full-width|sidebar
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_footer')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['portal_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('static_pages');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
