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
        Schema::create('course_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->json('titles')->nullable();
            $table->json('titles_ar')->nullable();
            $table->json('link_video')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('content_link')->nullable(); // لو فيديو يوتيوب أو لينك زووم
            $table->date('lession_date')->nullable(); // يوم الحصة
            $table->time('lession_time')->nullable(); // وقت الحصة
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            // لازم ينجح عشان يفتح الحصة التالية
            $table->boolean('must_pass_to_unlock')
                ->default(false);
            $table->boolean('must_solve_assignment_to_unlock')
                ->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_details');
    }
};
