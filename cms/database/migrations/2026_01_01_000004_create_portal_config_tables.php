<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'html', 'encrypted'])
                  ->default('string');
            $table->string('group')->default('general'); // general|seo|email|social|security
            $table->timestamps();

            $table->unique(['portal_id', 'key']);
            $table->index(['portal_id', 'group']);
        });

        Schema::create('portal_design_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('token_key');   // e.g. color-primary
            $table->string('token_value', 500);
            $table->enum('category', ['color', 'typography', 'spacing', 'layout', 'animation', 'custom'])
                  ->default('custom');
            $table->timestamps();

            $table->unique(['portal_id', 'token_key']);
            $table->index('portal_id');
        });

        Schema::create('portal_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
            $table->string('module');
            $table->boolean('enabled')->default(true);
            $table->json('config')->nullable(); // module-specific config
            $table->timestamps();

            $table->unique(['portal_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_modules');
        Schema::dropIfExists('portal_design_tokens');
        Schema::dropIfExists('portal_settings');
    }
};
