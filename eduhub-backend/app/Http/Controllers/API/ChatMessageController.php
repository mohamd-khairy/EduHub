<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request)
    {
        ChatMessage::when(request('chat_id'), function ($query, $chatId) {
            $query->where('chat_id', $chatId);
        })
            ->whereHas('chat', function ($query) {
                $query->where(function ($q) {
                    $q->where('sender_id', auth()->id())
                        ->orWhere('receiver_id', auth()->id());
                });
            })
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);
        $messages = ChatMessage::with('sender')
            ->when(request('chat_id'), function ($query) use ($request) {
                $query->where('chat_id', $request->chat_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 20));

        return $this->success($messages);
    }

    public function store(Request $request)
    {
        $sender = auth()->user();

        $message = ChatMessage::firstOrCreate([
            'chat_id' => $request->chat_id,
            'sender_type' => $sender->getMorphClass(),
            'sender_id' => $sender->id,
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        return $this->success($message);
    }
}
