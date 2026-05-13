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
        Schema::create('question_banks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('stage_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('subject_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('question_type', [
                'true_false',
                'multiple_choice',
                'essay'
            ]);

            $table->text('question');

            $table->text('correct_answer')->nullable();

            $table->decimal('mark', 8, 2)->default(1);

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
