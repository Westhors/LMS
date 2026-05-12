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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('code_parent')->nullable();
            $table->string('phone_parent')->nullable();
            $table->enum('type_of_attendance', ['center', 'online'])->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->foreignId('teacher_id')->nullable()
                ->constrained()
                ->nullOnDelete();
                
            $table->foreignId('stage_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('center_hour_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->boolean('active')->default(true);
            $table->decimal('balance', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
