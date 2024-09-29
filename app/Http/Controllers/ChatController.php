<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\OllamaController;

use App\Models\User;
use App\Models\Message;
use App\Models\Feedback;

class ChatController extends Controller
{
    public function handleMessage(Request $request)
    {
        if ($request->isJson()) {
            // Retrieve conversation_id from session or create a new one
            $conversationId = session("conversation_id", Str::uuid());
            session(["conversation_id" => $conversationId]);

            // Retrieve userId from session
            $userId = session("user_id");

            // Check if request is rating, and return
            if ($request->input("feedback")) {
                $rating = $request->input("feedback")["rating"];
                $comments = $request->input("feedback")["comments"];
                $feedbackResponse = $this->storeFeedback($conversationId, $userId, $rating, $comments);
                if ($feedbackResponse->original["success"]) {
                    session()->forget("conversation_id");
                    session()->forget("user_id");
                    return response()->json(["success" => true, "reply" => "Thank you for your feedback!"]);
                } else {
                    return response()->json(["success" => false, "error" => "There was an error storing your feedback."]);
                }
            }

            // get the message from the request
            $userMessage = $request->input("message");

            // Initialize OllamaController
            $ollama = new OllamaController();
            $response = $ollama->chat($request);

            // Check if the response contains a 'registered' message
            if (isset($response->original["name"])) {
                $registerResponse = $this->registerUser($response->original["name"]);
                $userId = $registerResponse->original["user"]->id;
                session(["user_id" => $userId]);
                return response()->json([
                    "reply" => "Thanks " . $response->original["name"] . "! How may I help you today?",
                ]);
            }

            // Store the messages in the database
            if ($userId) {
                $this->storeMessages($conversationId, $userId, $userMessage, $response->original["reply"]);
            }

            // Handle other types of responses from OllamaController
            return $response;
        } else {
            return response()->json(["error" => "Invalid request"], 400);
        }
    }

    /**
     * Register a new user
     */
    public function registerUser(string $name)
    {
        try {
            $user = User::create(["name" => $name]);
            return response()->json(["success" => true, "user" => $user]);
        } catch (\Exception $e) {
            info("Error registering user: " . $e->getMessage());
            return response()->json(["success" => false, "error" => $e->getMessage()]);
        }
    }

    /**
     * Save the message in the database
     */
    public function storeMessages(string $conversation_id, int $user_id, string $user_message, string $staff_reply)
    {
        try {
            Message::create([
                "user_id" => $user_id,
                "conversation_id" => $conversation_id,
                "user_message" => $user_message,
                "staff_reply" => $staff_reply,
            ]);
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            info("Error storing message: " . $e->getMessage());
            return response()->json(["success" => false, "error" => $e->getMessage()]);
        }
    }

    /**
     * Store feedback from the user about the conversation
     */
    public function storeFeedback(string $conversation_id, int $user_id, int $rating, string $comments)
    {
        try {
            Feedback::create([
                "user_id" => $user_id,
                "conversation_id" => $conversation_id,
                "rating" => $rating,
                "comments" => $comments,
            ]);
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            info("Error storing feedback: " . $e->getMessage());
            return response()->json(["success" => false, "error" => $e->getMessage()]);
        }
    }
}
