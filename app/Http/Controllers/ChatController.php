<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Models\Product;
use App\Models\Chat;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Tampilkan daftar percakapan aktif.
     */
    public function index(Request $request)
    {
        $chats = $this->chatService->getUserChats($request->user()->id);
        return view('chat.room', compact('chats'));
    }

    /**
     * Inisiasi chat dari halaman produk.
     * Menggunakan model binding Product $product untuk keamanan.
     */
    public function startChat(Request $request, Product $product)
    {
        // Pembeli tidak bisa chat diri sendiri
        if ($product->seller_id == $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa memulai chat dengan produk sendiri.');
        }

        $chat = $this->chatService->getOrCreateRoom($request->user()->id, $product->seller_id, $product->id);

        return redirect()->route('chat.room', $chat->id);
    }

    /**
     * Tampilkan detail chat tertentu.
     */
    public function room(Request $request, Chat $chat)
    {
        // Pastikan user adalah bagian dari chat ini
        if ($chat->user1_id != $request->user()->id && $chat->user2_id != $request->user()->id) {
            abort(403);
        }

        $chats = $this->chatService->getUserChats($request->user()->id);
        $messages = $this->chatService->getMessages($chat->id);
        
        return view('chat.room', compact('chats', 'messages', 'chat'));
    }

    /**
     * Kirim pesan baru (AJAX atau Form).
     */
    public function send(Request $request, Chat $chat)
    {
        $request->validate(['message' => 'required|string']);
        
        $msg = $this->chatService->sendMessage($chat->id, $request->user()->id, $request->message);
        $chat->touch();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back();
    }

    /**
     * Update pesan (Edit).
     */
    public function updateMessage(Request $request, ChatMessage $message)
    {
        if ($message->sender_id != $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['message' => 'required|string']);
        
        $this->chatService->updateMessage($message->id, $request->message);
        
        return response()->json(['success' => true]);
    }

    /**
     * Hapus pesan.
     */
    public function deleteMessage(Request $request, ChatMessage $message)
    {
        if ($message->sender_id != $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->chatService->deleteMessage($message->id);
        
        return response()->json(['success' => true]);
    }

    /**
     * Endpoint untuk polling pesan baru via AJAX.
     */
    public function getNewMessages(Request $request, Chat $chat)
    {
        $afterId = $request->query('after_id', 0);
        $messages = $this->chatService->getMessages($chat->id, $afterId);
        
        return response()->json([
            'messages' => $messages,
            'current_user_id' => $request->user()->id
        ]);
    }
}
