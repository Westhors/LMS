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
        Schema::create('payment_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_used')->default(false);
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_codes');
    }
};
