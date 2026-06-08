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

            $table->string('code')->unique();

            // نوع الكود
            $table->enum('type', ['wallet', 'course', 'semester', 'lesson']);
            
            $table->enum('type_code',  ['online', 'center'])->default('online');

            // علاقات مباشرة
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('course_detail_id')->nullable()->constrained()->cascadeOnDelete();

            // لو Wallet
            $table->decimal('amount', 10, 2)->nullable();

            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_used')->default(false);
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
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
