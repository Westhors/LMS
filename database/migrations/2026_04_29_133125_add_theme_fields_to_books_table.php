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
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0);
            $table->foreignId('stage_id')->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
        {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('discount');
                $table->dropColumn('stage_id');
            });
        }
};
