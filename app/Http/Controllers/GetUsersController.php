<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class GetUsersController extends Controller
{
    public function getUsers(Request $request)
    {
        // Get the query parameter from the request
        $query = $request->query("query");

        if ($query === "most_messages") {
            $users = User::withCount("messages") // Count the number of messages per user
                ->orderBy("messages_count", "desc") // Sort by the message count in descending order
                ->get();
            return response()->json($users);
        } else {
            $users = User::all();
            return response()->json($users);
        }
    }
}
