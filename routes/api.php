<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GetUsersController;
use App\Http\Controllers\GetConversationsController;
use App\Http\Controllers\GetMessagesController;


// API Route to send and receive messages from Ollama from frontend
Route::post("/chat", [ChatController::class, "handleMessage"]);

// API Routes to get data from the database
Route::get("/users", [GetUsersController::class, "getUsers"]);
Route::get("/conversations", [GetConversationsController::class, "getConversations"]);
Route::get("/messages", [GetMessagesController::class, "getMessages"]);
