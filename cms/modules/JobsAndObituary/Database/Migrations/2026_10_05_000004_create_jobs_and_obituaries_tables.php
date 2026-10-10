<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_circulars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1)->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('organization');
            $table->enum('job_type', ['govt', 'private', 'bank', 'ngo', 'education'])->default('govt');
            $table->string('vacancy_count')->nullable(); // e.g. "৫০ জন" or "নির্দিষ্ট নয়"
            $table->string('salary_range')->nullable();
            $table->string('location')->nullable();
            $table->date('deadline')->nullable();
            $table->string('application_url')->nullable();
            $table->text('description')->nullable();
            $table->string('circular_image')->nullable();
            $table->enum('status', ['published', 'draft', 'expired'])->default('published');
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('obituaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('portal_id')->default(1)->index();
            $table->string('deceased_name');
            $table->string('slug')->unique();
            $table->integer('age')->nullable();
            $table->date('death_date')->nullable();
            $table->string('passed_away_place')->nullable();
            $table->text('bio')->nullable();
            $table->text('funeral_details')->nullable(); // জানাজা ও দাফনের বিবরণ
            $table->string('photo')->nullable();
            $table->string('family_contact')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obituaries');
        Schema::dropIfExists('job_circulars');
    }
};
