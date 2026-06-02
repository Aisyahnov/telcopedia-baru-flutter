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
        $query = Chat::where(function ($q) use ($buyerId, $sellerId) {
                $q->where(function ($sq) use ($buyerId, $sellerId) {
                    $sq->where('user1_id', $buyerId)->where('user2_id', $sellerId);
                })->orWhere(function ($sq) use ($buyerId, $sellerId) {
                    $sq->where('user1_id', $sellerId)->where('user2_id', $buyerId);
                });
            });

        if ($productId) {
            $query->where('product_id', $productId);
        } else {
            $query->whereNull('product_id');
        }

        $chat = $query->first();

        if (!$chat) {
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

    public function getSellerChatGroups($sellerId)
    {
        // Ambil semua chat yang di mana user ini adalah salah satu pesertanya
        // DAN ada kaitan produknya (biasanya seller selalu dikaitkan dengan produk yang ditanya)
        // Kita kelompokkan berdasarkan product_id
        $chats = Chat::where(function($q) use ($sellerId) {
                        $q->where('user1_id', $sellerId)->orWhere('user2_id', $sellerId);
                    })
                    ->whereNotNull('product_id')
                    ->with(['product', 'user1', 'user2', 'messages' => function($q) {
                        $q->latest()->limit(1);
                    }])
                    ->orderBy('updated_at', 'desc')
                    ->get();

        $grouped = $chats->groupBy('product_id');
        
        $result = [];
        foreach ($grouped as $productId => $productChats) {
            $product = $productChats->first()->product;
            if (!$product) continue;

            $result[] = [
                'product' => $product,
                'chats' => $productChats->map(function($chat) use ($sellerId) {
                    return [
                        'id' => $chat->id,
                        'other_user' => ($chat->user1_id == $sellerId) ? $chat->user2 : $chat->user1,
                        'last_message' => $chat->messages->first(),
                        'updated_at' => $chat->updated_at
                    ];
                })
            ];
        }

        return $result;
    }
}
