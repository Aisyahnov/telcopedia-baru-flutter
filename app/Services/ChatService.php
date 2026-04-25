<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;

class ChatService
{
    /**
     * Cari atau buat room chat berdasarkan user dan produk tertentu.
     */
    public function getOrCreateRoom($buyerId, $sellerId, $productId)
    {
        // Cari room yang sudah ada antara kedua user ini (tanpa filter produk dulu)
        $chat = Chat::where(function ($q) use ($buyerId, $sellerId) {
                $q->where(function ($sq) use ($buyerId, $sellerId) {
                    $sq->where('user1_id', $buyerId)->where('user2_id', $sellerId);
                })->orWhere(function ($sq) use ($buyerId, $sellerId) {
                    $sq->where('user1_id', $sellerId)->where('user2_id', $buyerId);
                });
            })
            ->first();

        if ($chat) {
            // Jika room sudah ada, tapi produk yang dibahas berbeda, kita update konteks produknya
            if ($productId && $chat->product_id != $productId) {
                $chat->update(['product_id' => $productId]);
            }
        } else {
            // Jika benar-benar belum ada percakapan, buat baru
            $chat = Chat::create([
                'user1_id' => $buyerId,
                'user2_id' => $sellerId,
                'product_id' => $productId,
            ]);
        }

        return $chat;
    }

    public function sendMessage($chatId, $senderId, $messageText)
    {
        $chat = Chat::findOrFail($chatId);
        
        return ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $senderId,
            'message' => $messageText,
            'is_read' => false,
        ]);
    }

    public function updateMessage($messageId, $messageText)
    {
        $message = ChatMessage::findOrFail($messageId);
        $message->update(['message' => $messageText]);
        return $message;
    }

    public function deleteMessage($messageId)
    {
        $message = ChatMessage::findOrFail($messageId);
        return $message->delete();
    }

    public function getMessages($chatId, $afterId = 0)
    {
        $query = ChatMessage::where('chat_id', $chatId)
                    ->with('sender')
                    ->orderBy('created_at', 'asc');
        
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        return $query->get();
    }

    public function getUserChats($userId)
    {
        $chats = Chat::where('user1_id', $userId)
                   ->orWhere('user2_id', $userId)
                   ->with(['user1', 'user2', 'product', 'messages' => function($q) {
                       $q->latest()->limit(1);
                   }])
                   ->orderBy('updated_at', 'desc')
                   ->get();

        // Mengelompokkan berdasarkan pasangan user agar tidak ada duplikat di sidebar
        return $chats->unique(function ($chat) use ($userId) {
            $otherId = ($chat->user1_id == $userId) ? $chat->user2_id : $chat->user1_id;
            // Gunakan kombinasi ID yang urut agar A-B sama dengan B-A
            return min($userId, $otherId) . '-' . max($userId, $otherId);
        });
    }
}
