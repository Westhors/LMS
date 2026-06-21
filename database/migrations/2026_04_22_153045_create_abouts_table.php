<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('name_ar')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('facebook_meta')->nullable();
            $table->text('google_meta')->nullable();
            $table->text('tiktok_meta')->nullable();
            $table->text('you_tube_meta')->nullable();
            $table->string('facebook_count')->nullable();
            $table->string('google_count')->nullable();
            $table->string('tiktok_count')->nullable();
            $table->string('you_tube_count')->nullable();
            $table->boolean('active')->default(1);
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
