<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bangladesh administrative divisions (pre-seeded)
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // বিভাগ
            $table->string('name_en');
            $table->string('bn_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // জেলা
            $table->string('name_en');
            $table->string('bn_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // উপজেলা
            $table->string('name_en');
            $table->string('bn_code', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
    }
};
