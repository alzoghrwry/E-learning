<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // منع المحادثات المكررة بين نفس المستخدمين
            $table->unique(['student_id', 'admin_id']);
            
            // فهرسة للاستعلامات السريعة
            $table->index(['student_id', 'last_message_at']);
            $table->index(['admin_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};