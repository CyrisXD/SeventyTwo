<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("chat_messages", function (Blueprint $table) {
            $table->id();
            $table->uuid("conversation_id")->index(); // Group messages under a conversation
            $table->text("user_message"); // Storing the user's message
            $table->text("staff_reply")->nullable(); // Storing the staff/AI reply
            $table->timestamps(); // Adds created_at and updated_at fields

            // Set up foreign key constraints
            $table->foreignId("user_id")->nullable()->constrained("chat_users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("chat_messages");
    }
}
