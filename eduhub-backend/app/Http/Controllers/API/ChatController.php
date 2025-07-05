<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request)
    {
        $sender = auth()->user();
        $receiver_type = $request->receiver_type;
        $receiver_id = $request->receiver_id;
        $receiver = getModelFromType($receiver_type)->findOrFail($receiver_id);

        // Normalize participant pairs
        [$type1, $id1] = [$sender->getMorphClass(), $sender->id];
        [$type2, $id2] = [$receiver->getMorphClass(), $receiver->id];

        // Always sort the pair lexicographically (to match in any order)
        if (strcmp($type1 . $id1, $type2 . $id2) > 0) {
            [$type1, $id1, $type2, $id2] = [$type2, $id2, $type1, $id1];
        }

        $chat = Chat::firstOrCreate([
            'sender_type' => $type1,
            'sender_id' => $id1,
            'receiver_type' => $type2,
            'receiver_id' => $id2,
        ]);

        return $this->success($chat);
    }
}
