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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['assignment', 'exam'])->default('exam');

            $table->foreignId('course_detail_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('stage_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('teacher_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->integer('total_marks')->default(0);
            $table->integer('duration_minutes')->nullable();

            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
