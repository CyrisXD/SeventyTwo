<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Feedback;

class GetConversationsController extends Controller
{
    public function getConversations(Request $request)
    {
        // Get the query parameter from the request
        $query = $request->query("query");

        if ($query === "highest_rated") {
            $conversations = Feedback::select("conversation_id", "rating")
                ->orderByDesc("rating")
                ->limit(10) 
                ->get();

            // Fetch the corresponding messages for these conversations
            $conversationIds = $conversations->pluck("conversation_id");
            $messages = Message::whereIn("conversation_id", $conversationIds)->get()->groupBy("conversation_id");

            // Combine feedback and messages
            $result = $conversations->map(function ($feedback) use ($messages) {
                return [
                    "conversation_id" => $feedback->conversation_id,
                    "rating" => $feedback->rating,
                    "messages" => $messages[$feedback->conversation_id] ?? [],
                ];
            });

            return response()->json($result);
        } else {
            $conversations = Message::all();
            return response()->json($conversations);
        }
    }
}
