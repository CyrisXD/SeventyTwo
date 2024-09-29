<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Feedback;

class GetMessagesController extends Controller
{
    public function getMessages(Request $request)
    {
        $messages = Message::all();
        return response()->json($messages);
    }
}
