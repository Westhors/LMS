<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_instances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('teacher_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('instance_id')->unique();

            $table->string('access_token');

            $table->string('phone')->nullable();

            $table->enum('status', [
                'pending',
                'connected',
                'disconnected'
            ])->default('pending');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_instances');
    }

};
