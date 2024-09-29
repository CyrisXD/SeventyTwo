<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "chat_messages";
    // Fillable fields to allow mass assignment
    protected $fillable = ["user_id", "conversation_id", "user_message", "staff_reply"];

    // A message belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
