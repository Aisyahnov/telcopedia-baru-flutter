<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index(Request $request)
    {
        $rooms = $this->chatService->getUserChats($request->user()->id);
        return response()->json(['data' => $rooms]);
    }

    public function room($chat)
    {
        $messages = $this->chatService->getMessages($chat);
        return response()->json(['data' => $messages]);
    }

    public function messages($chatId)
    {
        $messages = $this->chatService->getMessages($chatId);
        return response()->json(['data' => $messages]);
    }

    public function send(Request $request, $chat)
    {
        $request->validate(['message' => 'required|string']);
        $message = $this->chatService->sendMessage($chat, $request->user()->id, $request->message);
        return response()->json(['data' => $message]);
    }

    public function updateMessage(Request $request, $messageId)
    {
        return response()->json(['message' => 'Message updated (simulated)']);
    }

    public function deleteMessage($messageId)
    {
        return response()->json(['message' => 'Message deleted (simulated)']);
    }
}
