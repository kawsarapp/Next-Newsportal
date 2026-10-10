<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Closure table for infinite nesting — O(1) tree queries
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('depth')->default(0);

            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 10)->nullable(); // hex color
            $table->string('thumbnail')->nullable();

            // Type: section|tag|series|district|campaign|custom
            $table->string('type')->default('section');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_nav')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['portal_id', 'slug']);
            $table->index(['portal_id', 'parent_id', 'order']);
            $table->index(['portal_id', 'type', 'is_active']);
        });

        // Closure table for efficient tree traversal
        Schema::create('category_closures', function (Blueprint $table) {
            $table->foreignId('ancestor_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('descendant_id')->constrained('categories')->cascadeOnDelete();
            $table->integer('depth');

            $table->primary(['ancestor_id', 'descendant_id']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('type')->default('tag'); // tag|topic|series
            $table->integer('post_count')->default(0); // denormalized for performance
            $table->timestamps();

            $table->unique(['portal_id', 'slug']);
            $table->index(['portal_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_closures');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
    }
};
