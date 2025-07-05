<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function store(Request $request)
    {
        $sender_type = $request->sender_type;
        $sender_id = $request->sender_id;
        $sender = getModelFromType($sender_type)->findOrFail($sender_id);

        $message = ChatMessage::firstOrCreate([
            'chat_id' => $request->chat_id,
            'sender_type' => $sender->getMorphClass(),
            'sender_id' => $sender_id,
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        return $this->success($message);
    }
}
