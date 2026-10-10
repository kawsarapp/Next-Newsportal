<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1)->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('time_limit_minutes')->default(5);
            $table->integer('passing_score')->default(60); // percentage
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('total_attempts')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('question');
            $table->json('options'); // array of options: [{"text": "...", "is_correct": true}]
            $table->text('explanation')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('participant_name')->nullable();
            $table->string('participant_email')->nullable();
            $table->integer('score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->boolean('passed')->default(false);
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
