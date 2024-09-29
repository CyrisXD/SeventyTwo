<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = "chat_feedback";
    protected $fillable = ["user_id", "conversation_id", "rating", "comments"];

    // Feedback belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Feedback is linked to a conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, "conversation_id", "id");
    }
}
