<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ChatbotService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function handleMessage($message, $history = [])
    {
        // 1. If API key is missing, use basic keyword matching
        if (empty($this->apiKey)) {
            return $this->fallbackResponse($message);
        }

        // 2. Prepare prompt
        $systemPrompt = "Kamu adalah TelcoBot, asisten belanja pintar dan Customer Service untuk Telcopedia (Marketplace khusus mahasiswa Telkom University).
Tugasmu:
- Menjawab dengan ramah, santai khas mahasiswa (pakai aku/kamu atau gue/lu yang sopan).
- Membantu pembeli menemukan barang bekas atau baru dengan harga mahasiswa.
- Menjawab seputar FAQ: COD di kampus, aman, terpercaya, khusus mahasiswa Telkom.
- JIKA pengguna ingin mencari barang, TULISKAN tag [SEARCH:kata kunci] di AKHIR pesanmu. Contoh: 'Ini aku cariin laptop ya! [SEARCH:laptop]'. Jangan berikan tag jika pengguna tidak mencari barang.";

        $contents = [];
        // Optional: add history here if we store it. For now, just send current message and system instructions.
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "Instruksi Sistem: " . $systemPrompt . "\n\nPesan Pengguna: " . $message]]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => $contents,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Maaf, aku lagi bingung nih. Coba tanya lagi ya!";
                
                return $this->processReply($replyText);
            }

            \Log::error('Gemini API Error: ' . $response->body());
            return $this->fallbackResponse($message);

        } catch (\Exception $e) {
            \Log::error('Chatbot Exception: ' . $e->getMessage());
            return $this->fallbackResponse($message);
        }
    }

    protected function processReply($replyText)
    {
        $products = [];
        
        // Extract [SEARCH:keyword]
        if (preg_match('/\[SEARCH:(.*?)\]/i', $replyText, $matches)) {
            $keyword = trim($matches[1]);
            // Remove the tag from reply
            $replyText = str_replace($matches[0], '', $replyText);
            
            // Search products
            $products = Product::where('status', 'approved')
                ->where(function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
                })
                ->with('category', 'seller')
                ->latest()
                ->take(4)
                ->get();
        }

        return [
            'text' => trim($replyText),
            'products' => $products
        ];
    }

    protected function fallbackResponse($message)
    {
        $msg = strtolower($message);
        $reply = "Halo! Aku TelcoBot. Maaf ya, saat ini aku sedang dalam mode pemeliharaan dan belum bisa diajak ngobrol panjang. Tapi aku bisa bantu carikan barang kok, ketik aja 'cari [nama barang]'.";
        $products = [];

        if (str_contains($msg, 'cari') || str_contains($msg, 'buku') || str_contains($msg, 'laptop')) {
            $reply = "Oke, ini beberapa produk yang mungkin kamu suka (mode pencarian otomatis):";
            $search = str_replace('cari', '', $msg);
            $products = Product::where('status', 'approved')->latest()->take(4)->get();
        } elseif (str_contains($msg, 'retur') || str_contains($msg, 'kembali')) {
            $reply = "Untuk retur barang, kamu bisa ke halaman Riwayat Pesanan, lalu klik 'Ajukan Retur' di produk yang udah berstatus Selesai atau Dikirim ya.";
        }

        return [
            'text' => $reply,
            'products' => $products
        ];
    }
}
