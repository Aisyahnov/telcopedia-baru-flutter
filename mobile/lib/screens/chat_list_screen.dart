import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:timeago/timeago.dart' as timeago;
import '../services/chat_service.dart';
import '../models/chat.dart';
import '../providers/auth_provider.dart';

class ChatListScreen extends StatefulWidget {
  const ChatListScreen({super.key});

  @override
  State<ChatListScreen> createState() => _ChatListScreenState();
}

class _ChatListScreenState extends State<ChatListScreen> {
  final ChatService _chatService = ChatService();
  List<Chat> _chats = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadChats();
  }

  Future<void> _loadChats() async {
    final chats = await _chatService.getChats();
    if (mounted) {
      setState(() {
        _chats = chats;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final currentUserId = Provider.of<AuthProvider>(context).user?.id;

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Text(
          'Pesan Masuk',
          style: GoogleFonts.plusJakartaSans(
            color: Colors.black,
            fontWeight: FontWeight.w900,
            fontSize: 20,
          ),
        ),
        centerTitle: true,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.black87),
            onPressed: _loadChats,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : _chats.isEmpty
              ? _buildEmptyState()
              : RefreshIndicator(
                  onRefresh: _loadChats,
                  color: const Color(0xFF9F1521),
                  child: ListView.builder(
                    itemCount: _chats.length,
                    itemBuilder: (context, index) {
                      final chat = _chats[index];
                      final partner = chat.user1Id == currentUserId ? chat.user2 : chat.user1;
                      final lastMsg = chat.messages.isNotEmpty ? chat.messages.last : null;
                      
                      return ListTile(
                        onTap: () async {
                          await Navigator.pushNamed(context, '/chat/room', arguments: chat);
                          _loadChats(); // Reload when back
                        },
                        leading: CircleAvatar(
                          radius: 28,
                          backgroundColor: const Color(0xFF9F1521).withValues(alpha: 0.1),
                          backgroundImage: partner?.photo != null
                              ? NetworkImage(ApiService.getImageUrl('storage/${partner!.photo}'))
                              : null,
                          child: partner?.photo == null
                              ? const Icon(Icons.person, color: Color(0xFF9F1521))
                              : null,
                        ),
                        title: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                partner?.name ?? 'Pengguna Telcopedia',
                                style: GoogleFonts.plusJakartaSans(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 15,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            if (lastMsg != null)
                              Text(
                                timeago.format(lastMsg.createdAt, locale: 'id_short'),
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  color: Colors.grey,
                                ),
                              ),
                          ],
                        ),
                        subtitle: Row(
                          children: [
                            if (lastMsg?.senderId == currentUserId)
                              const Icon(Icons.done_all, size: 14, color: Colors.blue),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                lastMsg?.message ?? 'Mulai obrolan...',
                                style: GoogleFonts.plusJakartaSans(
                                  fontSize: 13,
                                  color: Colors.grey.shade600,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            if (lastMsg != null && !lastMsg.isRead && lastMsg.senderId != currentUserId)
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: const Color(0xFF9F1521),
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Text(
                                  'BARU',
                                  style: GoogleFonts.plusJakartaSans(
                                    color: Colors.white,
                                    fontSize: 8,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                          ],
                        ),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                      );
                    },
                  ),
                ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.comments_disabled_outlined, size: 80, color: Colors.grey.shade300),
            const SizedBox(height: 20),
            Text(
              'Belum ada obrolan.',
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.bold,
                fontSize: 18,
                color: Colors.black87,
              ),
            ),
            const SizedBox(height: 10),
            Text(
              'Tanyakan kondisi barang langsung ke penjual dengan fitur chat.',
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(color: Colors.grey),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              onPressed: () => Navigator.pushReplacementNamed(context, '/home'),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF9F1521),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
                padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 12),
              ),
              child: const Text('Mulai Belanja'),
            ),
          ],
        ),
      ),
    );
  }
}
