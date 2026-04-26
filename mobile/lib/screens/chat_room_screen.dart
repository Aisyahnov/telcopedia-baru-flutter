import 'dart:async';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../services/chat_service.dart';
import '../models/chat.dart';
import '../providers/auth_provider.dart';

class ChatRoomScreen extends StatefulWidget {
  final Chat chat;
  const ChatRoomScreen({super.key, required this.chat});

  @override
  State<ChatRoomScreen> createState() => _ChatRoomScreenState();
}

class _ChatRoomScreenState extends State<ChatRoomScreen> {
  final ChatService _chatService = ChatService();
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  List<ChatMessage> _messages = [];
  Timer? _pollingTimer;
  int? _editingMessageId;
  bool _isSending = false;

  @override
  void initState() {
    super.initState();
    _messages = widget.chat.messages;
    _startPolling();
    WidgetsBinding.instance.addPostFrameCallback((_) => _scrollToBottom());
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _startPolling() {
    _pollingTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
      _fetchNewMessages();
    });
  }

  Future<void> _fetchNewMessages() async {
    final lastId = _messages.isNotEmpty ? _messages.last.id : 0;
    final newMsgs = await _chatService.getMessages(widget.chat.id, afterId: lastId);
    if (mounted && newMsgs.isNotEmpty) {
      setState(() {
        _messages.addAll(newMsgs);
      });
      _scrollToBottom();
    }
  }

  void _scrollToBottom() {
    if (_scrollController.hasClients) {
      _scrollController.animateTo(
        _scrollController.position.maxScrollExtent,
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeOut,
      );
    }
  }

  Future<void> _handleSend() async {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;

    setState(() => _isSending = true);

    if (_editingMessageId != null) {
      final success = await _chatService.updateMessage(_editingMessageId!, text);
      if (success) {
        setState(() {
          final idx = _messages.indexWhere((m) => m.id == _editingMessageId);
          if (idx != -1) _messages[idx].message = text;
          _editingMessageId = null;
          _messageController.clear();
        });
      }
    } else {
      final success = await _chatService.sendMessage(widget.chat.id, text);
      if (success) {
        _messageController.clear();
        _fetchNewMessages();
      }
    }

    if (mounted) setState(() => _isSending = false);
  }

  void _enterEditMode(ChatMessage msg) {
    setState(() {
      _editingMessageId = msg.id;
      _messageController.text = msg.message;
    });
  }

  Future<void> _handleDelete(int msgId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus Pesan?'),
        content: const Text('Pesan yang dihapus tidak bisa dikembalikan.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(c, true), child: const Text('Hapus', style: TextStyle(color: Colors.red))),
        ],
      ),
    );

    if (confirm == true) {
      final success = await _chatService.deleteMessage(msgId);
      if (success && mounted) {
        setState(() {
          _messages.removeWhere((m) => m.id == msgId);
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final currentUserId = Provider.of<AuthProvider>(context).user?.id;
    final partner = widget.chat.user1Id == currentUserId ? widget.chat.user2 : widget.chat.user1;

    return Scaffold(
      backgroundColor: const Color(0xFFF0F2F5),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        title: Row(
          children: [
            CircleAvatar(
              radius: 18,
              backgroundImage: partner?.photo != null
                  ? NetworkImage('http://10.0.2.2:8000/storage/${partner!.photo}')
                  : null,
              child: partner?.photo == null ? const Icon(Icons.person) : null,
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(partner?.name ?? 'User', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.black87)),
                  Text('Online', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.green, fontWeight: FontWeight.bold)),
                ],
              ),
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
              padding: const EdgeInsets.all(15),
              itemCount: _messages.length,
              itemBuilder: (context, index) {
                final msg = _messages[index];
                final isMe = msg.senderId == currentUserId;
                return _buildMessageBubble(msg, isMe);
              },
            ),
          ),
          _buildInputArea(),
        ],
      ),
    );
  }

  Widget _buildProductBadge() {
    final p = widget.chat.product!;
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 5)],
      ),
      child: Row(
        children: [
          Container(
            width: 45, height: 45,
            decoration: BoxDecoration(color: const Color(0xFF9F1521).withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
            child: const Icon(Icons.shopping_bag_outlined, color: Color(0xFF9F1521)),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(p.name, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold), maxLines: 1, overflow: TextOverflow.ellipsis),
                Text(formatter.format(p.price), style: GoogleFonts.plusJakartaSans(fontSize: 11, color: const Color(0xFF9F1521), fontWeight: FontWeight.bold)),
              ],
            ),
          ),
          TextButton(
            onPressed: () {}, // Link to product
            child: Text('Kunjungi', style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.black87)),
          ),
        ],
      ),
    );
  }

  Widget _buildMessageBubble(ChatMessage msg, bool isMe) {
    return Align(
      alignment: isMe ? Alignment.centerRight : Alignment.centerLeft,
      child: GestureDetector(
        onLongPress: isMe ? () {
          showModalBottomSheet(
            context: context,
            builder: (c) => SafeArea(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  ListTile(leading: const Icon(Icons.edit), title: const Text('Ubah Pesan'), onTap: () { Navigator.pop(c); _enterEditMode(msg); }),
                  ListTile(leading: const Icon(Icons.delete, color: Colors.red), title: const Text('Hapus Pesan', style: TextStyle(color: Colors.red)), onTap: () { Navigator.pop(c); _handleDelete(msg.id); }),
                ],
              ),
            ),
          );
        } : null,
        child: Container(
          margin: const EdgeInsets.symmetric(vertical: 5),
          padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
          decoration: BoxDecoration(
            color: isMe ? const Color(0xFF9F1521) : Colors.white,
            borderRadius: BorderRadius.only(
              topLeft: const Radius.circular(20),
              topRight: const Radius.circular(20),
              bottomLeft: isMe ? const Radius.circular(20) : const Radius.circular(5),
              bottomRight: isMe ? const Radius.circular(5) : const Radius.circular(20),
            ),
          ),
          constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.7),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                msg.message,
                style: GoogleFonts.plusJakartaSans(
                  color: isMe ? Colors.white : Colors.black87,
                  fontSize: 14,
                ),
              ),
              const SizedBox(height: 5),
              Text(
                DateFormat('HH:mm').format(msg.createdAt),
                style: GoogleFonts.plusJakartaSans(
                  color: isMe ? Colors.white70 : Colors.grey,
                  fontSize: 9,
                ),
              ),
            ],
          ),
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
              padding: const EdgeInsets.only(bottom: 8.0),
              child: Row(
                children: [
                  const Icon(Icons.edit, size: 14, color: Colors.amber),
                  const SizedBox(width: 5),
                  const Text('Mode Edit', style: TextStyle(fontSize: 11, color: Colors.amber, fontWeight: FontWeight.bold)),
                  const Spacer(),
                  GestureDetector(
                    onTap: () => setState(() { _editingMessageId = null; _messageController.clear(); }),
                    child: const Text('Batalkan', style: TextStyle(fontSize: 11, color: Colors.red, fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
          Row(
            children: [
              Expanded(
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  decoration: BoxDecoration(color: const Color(0xFFF0F2F5), borderRadius: BorderRadius.circular(30)),
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'Tulis pesan...',
                      hintStyle: GoogleFonts.plusJakartaSans(fontSize: 14, color: Colors.grey),
                      border: InputBorder.none,
                    ),
                    maxLines: null,
                  ),
                ),
              ),
              const SizedBox(width: 10),
              GestureDetector(
                onTap: _isSending ? null : _handleSend,
                child: Container(
                  width: 45, height: 45,
                  decoration: const BoxDecoration(color: Color(0xFF9F1521), shape: BoxShape.circle),
                  child: _isSending 
                    ? const Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : Icon(_editingMessageId != null ? Icons.check : Icons.send, color: Colors.white, size: 20),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
