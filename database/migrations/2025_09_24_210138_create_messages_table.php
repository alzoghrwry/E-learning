<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // للمرفقات أو المعلومات الإضافية
            $table->timestamps();

            // فهرسة للاستعلامات السريعة
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
            $table->index(['is_read', 'conversation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};