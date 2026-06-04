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
        Schema::create('center_hours', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('date')->nullable();
            $table->string('hours_start')->nullable();
            $table->string('hours_end')->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_hours');
    }
};
