<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("chat_feedback", function (Blueprint $table) {
            $table->id();
            $table->integer("rating"); // Rating (1-5 or other scale)
            $table->text("comments")->nullable(); // Optional comments for the feedback
            $table->uuid("conversation_id")->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreignId("user_id")->nullable()->constrained("chat_users")->onDelete("cascade");
            $table->foreign("conversation_id")->references("conversation_id")->on("chat_messages")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("chat_feedback");
    }
}
