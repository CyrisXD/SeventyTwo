<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "chat_users";
    protected $fillable = ["name"];
    // A user can have many messages
    public function messages()
    {
        return $this->hasMany(Message::class, "user_id");
    }

    // A user can provide feedback on many conversations
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }
}
