import 'dart:async';
import 'package:flutter/material.dart';
import '../../models/chat.dart';
import '../../models/user.dart';
import '../../services/chat_service.dart';
import '../../services/auth_service.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class ChatRoomScreen extends StatefulWidget {
  final Chat chat;
  const ChatRoomScreen({super.key, required this.chat});

  @override
  State<ChatRoomScreen> createState() => _ChatRoomScreenState();
}

class _ChatRoomScreenState extends State<ChatRoomScreen> {
  final ChatService _chatService = ChatService();
  final AuthService _authService = AuthService();
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  
  List<ChatMessage> _messages = [];
  User? _currentUser;
  bool _isLoading = true;
  Timer? _pollingTimer;
  int? _editingMessageId;

  @override
  void initState() {
    super.initState();
    _messages = widget.chat.messages;
    _loadUser();
    _startPolling();
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _loadUser() async {
    final user = await _authService.getCurrentUser();
    if (mounted) {
      setState(() {
        _currentUser = user;
        _isLoading = false;
      });
      _scrollToBottom();
    }
  }

  void _startPolling() {
    _pollingTimer = Timer.periodic(const Duration(seconds: 4), (timer) async {
      final lastId = _messages.isNotEmpty ? _messages.last.id : 0;
      final newMsgs = await _chatService.getMessages(widget.chat.id, afterId: lastId);
      if (newMsgs.isNotEmpty && mounted) {
        setState(() => _messages.addAll(newMsgs));
        _scrollToBottom();
      }
    });
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(_scrollController.position.maxScrollExtent, duration: const Duration(milliseconds: 300), curve: Curves.easeOut);
      }
    });
  }

  Future<void> _sendMessage() async {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;

    if (_editingMessageId != null) {
      final success = await _chatService.updateMessage(_editingMessageId!, text);
      if (success) {
        setState(() {
          final idx = _messages.indexWhere((m) => m.id == _editingMessageId);
          if (idx != -1) _messages[idx].message = text;
          _editingMessageId = null;
        });
      }
    } else {
      final success = await _chatService.sendMessage(widget.chat.id, text);
      if (success) {
        final lastId = _messages.isNotEmpty ? _messages.last.id : 0;
        final newMsgs = await _chatService.getMessages(widget.chat.id, afterId: lastId);
        setState(() => _messages.addAll(newMsgs));
        _scrollToBottom();
      }
    }
    _messageController.clear();
  }

  Future<void> _deleteMessage(int id) async {
    final success = await _chatService.deleteMessage(id);
    if (success && mounted) {
      setState(() => _messages.removeWhere((m) => m.id == id));
    }
  }

  @override
  Widget build(BuildContext context) {
    final partner = widget.chat.user1Id == _currentUser?.id ? widget.chat.user2 : widget.chat.user1;

    return Scaffold(
      backgroundColor: const Color(0xFFF0F2F5),
      appBar: AppBar(
        title: Row(
          children: [
            CircleAvatar(radius: 16, backgroundColor: const Color(0xFF9F1521).withOpacity(0.1), child: const Icon(Icons.person, size: 18, color: Color(0xFF9F1521))),
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(partner?.name ?? 'User', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.white)),
                Row(
                  children: [
                    const Icon(Icons.circle, size: 6, color: Colors.green),
                    const SizedBox(width: 4),
                    Text('ONLINE', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.green)),
                  ],
                ),
              ],
            ),
          ],
        ),
      ),
      body: Column(
        children: [
          if (widget.chat.product != null) _buildProductBadge(),
          Expanded(
            child: ListView.builder(
              controller: _scrollController,
              padding: const EdgeInsets.all(20),
              itemCount: _messages.length,
              itemBuilder: (context, index) => _buildMessageBubble(_messages[index]),
            ),
          ),
          _buildInputArea(),
        ],
      ),
    );
  }

  Widget _buildProductBadge() {
    final p = widget.chat.product!;
    return Container(
      margin: const EdgeInsets.all(15),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)]),
      child: Row(
        children: [
          ClipRRect(borderRadius: BorderRadius.circular(8), child: Image.network(p.imageUrl ?? '', width: 40, height: 40, fit: BoxFit.cover, errorBuilder: (c, e, s) => Container(width: 40, height: 40, color: Colors.grey.shade100))),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(p.name, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold), maxLines: 1, overflow: TextOverflow.ellipsis),
                Text(NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(p.price), style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
              ],
            ),
          ),
          ElevatedButton(
            onPressed: () {}, // Navigate to product
            style: ElevatedButton.styleFrom(backgroundColor: Colors.black, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)), minimumSize: const Size(0, 28), padding: const EdgeInsets.symmetric(horizontal: 12)),
            child: const Text('Kunjungi', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildMessageBubble(ChatMessage msg) {
    final isMe = msg.senderId == _currentUser?.id;
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: Column(
        crossAxisAlignment: isMe ? CrossAxisAlignment.end : CrossAxisAlignment.start,
        children: [
          GestureDetector(
            onLongPress: isMe ? () => _showActions(msg) : null,
            child: Container(
              constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: isMe ? const Color(0xFF9F1521) : Colors.white,
                borderRadius: BorderRadius.only(topLeft: const Radius.circular(15), topRight: const Radius.circular(15), bottomLeft: Radius.circular(isMe ? 15 : 4), bottomRight: Radius.circular(isMe ? 4 : 15)),
                boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 5, offset: const Offset(0, 2))],
              ),
              child: Column(
                crossAxisAlignment: isMe ? CrossAxisAlignment.end : CrossAxisAlignment.start,
                children: [
                  Text(msg.message, style: GoogleFonts.plusJakartaSans(fontSize: 13, color: isMe ? Colors.white : const Color(0xFF1A1A1A), height: 1.4)),
                  const SizedBox(height: 4),
                  Text(DateFormat('HH:mm').format(msg.createdAt), style: GoogleFonts.plusJakartaSans(fontSize: 8, color: isMe ? Colors.white.withOpacity(0.7) : Colors.grey)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _showActions(ChatMessage msg) {
    showModalBottomSheet(
      context: context,
      builder: (context) => Container(
        padding: const EdgeInsets.symmetric(vertical: 20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(leading: const Icon(Icons.edit_outlined), title: const Text('Ubah Pesan'), onTap: () { Navigator.pop(context); setState(() { _editingMessageId = msg.id; _messageController.text = msg.message; }); }),
            ListTile(leading: const Icon(Icons.delete_outline, color: Colors.red), title: const Text('Hapus Pesan', style: TextStyle(color: Colors.red)), onTap: () { Navigator.pop(context); _deleteMessage(msg.id); }),
          ],
        ),
      ),
    );
  }

  Widget _buildInputArea() {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: const BoxDecoration(color: Colors.white, border: Border(top: BorderSide(color: Color(0xFFEEEEEE)))),
      child: Column(
        children: [
          if (_editingMessageId != null)
            Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Row(
                children: [
                  const Icon(Icons.edit_outlined, size: 14, color: Colors.orange),
                  const SizedBox(width: 8),
                  Text('Mode Edit:', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.orange, fontWeight: FontWeight.bold)),
                  const Spacer(),
                  GestureDetector(onTap: () => setState(() => _editingMessageId = null), child: const Text('Batalkan', style: TextStyle(fontSize: 10, color: Colors.red, fontWeight: FontWeight.bold))),
                ],
              ),
            ),
          Row(
            children: [
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 15),
                  decoration: BoxDecoration(color: const Color(0xFFF0F2F5), borderRadius: BorderRadius.circular(25)),
                  child: TextField(
                    controller: _messageController,
                    decoration: const InputDecoration(hintText: 'Tulis pesan...', border: InputBorder.none, hintStyle: TextStyle(fontSize: 13)),
                    style: const TextStyle(fontSize: 13),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              InkWell(
                onTap: _sendMessage,
                child: Container(
                  width: 42,
                  height: 42,
                  decoration: const BoxDecoration(color: Color(0xFF9F1521), shape: BoxShape.circle),
                  child: Icon(_editingMessageId != null ? Icons.check : Icons.send, color: Colors.white, size: 20),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
