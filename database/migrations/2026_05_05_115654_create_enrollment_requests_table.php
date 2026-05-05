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
        Schema::create('enrollment_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained();
            $table->foreignId('teacher_id')->constrained();

            $table->enum('type', ['course', 'semester', 'lesson']);

            $table->foreignId('course_id')->nullable();
            $table->foreignId('semester_id')->nullable();
            $table->foreignId('course_detail_id')->nullable();

            $table->decimal('price', 10, 2);

            $table->enum('status', ['pending', 'rejected', 'approved'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_requests');
    }
};
