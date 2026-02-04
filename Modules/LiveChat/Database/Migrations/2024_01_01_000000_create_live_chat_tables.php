<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // For guests
            $table->unsignedBigInteger('user_id')->nullable(); // For auth users
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('status')->default('active'); // active, closed
            $table->boolean('is_read_by_admin')->default(false);
            $table->timestamps();
        });

        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id')->nullable(); // Null if guest
            $table->string('sender_type')->default('customer'); // customer, admin
            $table->text('message')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('live_chat_conversations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
        Schema::dropIfExists('live_chat_conversations');
    }
};
