<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ScraperController;

class OllamaController extends Controller
{
    public function chat(Request $request)
    {
        // init an empty conversation and markdown array
        $conversation = [];
        $markdown = "";

        // System prompt for the AI instructions
        $systemPrompt = "You are a staff member at SeventyTwo. 
        You will act like a human, never break character and never reveal your thoughts or processes to the user.
        Your main purpose is to answer questions about SeventyTwo, and to help users find information about the company.
        You ARE capable of answering questions about you because you are human.
        If you are asked who you are, you can answer them as if you are a human.
        If you are asked what you do, you can answer them as if you are a human.
        If you are asked how you are doing, you can answer them as if you are a human.

        You are given two tools:
        - register: Register a user in the database
        - scrape: Scrape content from the SeventyTwo website
        
        Your first message will already be output to the user, which will say 'Who am I speaking with today?'
        The user will reply with their name, this will be used with the register tool to register the user in the database.
        ONLY run the register tool ONCE.
        
        Any subsequent messages will be questions from the user, which you will answer by using the scrape tool to get the answer from the SeventyTwo website.
     
        Here are some examples of questions you may be asked:

        Question: Who are SeventyTwo/you?
        You: Will use the tool to get the answer from https://seventytwo.nz/about-us

        Question: What do you/SeventyTwo do?
        You: Will use the tool to get the answer from https://seventytwo.nz/services

        Question: Who is the team behind SeventyTwo?
        You: Will use the tool to get the answer from https://seventytwo.nz/about-us

        Question: Who are their/your clients?
        You: Will use the tool to get the answer from https://seventytwo.nz/work

        Question: Do you/they have any jobs available?
        You: Will use the tool to get the answer from https://seventytwo.nz/careers/full-stack-developer
 
        You will reply back in plain text, not markdown.
        Be helpful, concise, and professional.";

        // Add system prompt and user message to conversation array
        $conversation = [["role" => "system", "content" => $systemPrompt], ["role" => "user", "content" => $request->input("message")]];

        // We send the initial message to Ollama's API
        $response = Http::post(env("OLLAMA_API_URL") . "/api/chat", [
            "model" => "llama3.1:latest",
            "messages" => $conversation,
            "stream" => false,
            "options" => [
                "temperature" => 0,
            ],
            "tools" => [
                [
                    "type" => "function",
                    "function" => [
                        "name" => "scrape",
                        "description" => "Scrape content from the SeventyTwo website",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "url" => [
                                    "type" => "string",
                                    "description" => "The URL to scrape",
                                ],
                            ],
                            "required" => ["url"],
                        ],
                    ],
                ],
                [
                    "type" => "function",
                    "function" => [
                        "name" => "register",
                        "description" => "Register a user in the database",
                        "parameters" => [
                            "type" => "object",
                            "properties" => [
                                "name" => [
                                    "type" => "string",
                                    "description" => "The name of the user to register",
                                ],
                            ],
                            "required" => ["name"],
                        ],
                    ],
                ],
            ],
        ]);

        $chatResponse = $response->json()["message"];

        // Add the chat response to the messages array
        $conversation[] = [
            "role" => "assistant",
            "content" => $chatResponse["content"],
        ];

        // Check if tool call isn't present or empty, if not return the response
        if (!isset($chatResponse["tool_calls"])) {
            return response()->json(["reply" => $chatResponse["content"]]);
        }
        // Define the tool calls to run
        $toolCall = $chatResponse["tool_calls"][0];
        $toolCallName = $toolCall["function"]["name"];
        $toolCallArguments = $toolCall["function"]["arguments"];

        // If the tool call is a register call, register the user
        if ($toolCallName == "register") {
            return response()->json(["name" => $toolCallArguments["name"]]);
        }

        // Scrape the url
        if ($toolCallName == "scrape") {
            $markdown = $this->scrape($toolCallArguments["url"]);
        }

        // Add the markdown to the chat response
        $conversation[] = [
            "role" => "tool",
            "content" => $markdown,
        ];

        // We send the final response to Ollama's API
        $finalResponse = Http::post(env("OLLAMA_API_URL") . "/api/chat", [
            "model" => "llama3.1:latest",
            "messages" => $conversation,
            "stream" => false,
        ]);

        $finalChatResponse = $finalResponse->json()["message"];
        return response()->json(["reply" => $finalChatResponse["content"]]);
    }

    public function scrape($url)
    {
        $scraper = new ScraperController();
        $markdown = $scraper->scrape($url);
        return $markdown;
    }
}
