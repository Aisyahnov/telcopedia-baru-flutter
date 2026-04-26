import 'package:flutter/material.dart';
import '../../models/chat.dart';
import '../../models/user.dart';
import '../../services/chat_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class SellerChatListScreen extends StatefulWidget {
  const SellerChatListScreen({super.key});

  @override
  State<SellerChatListScreen> createState() => _SellerChatListScreenState();
}

class _SellerChatListScreenState extends State<SellerChatListScreen> {
  final ChatService _chatService = ChatService();
  final AuthService _authService = AuthService();
  List<Chat> _chats = [];
  User? _user;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final user = await _authService.getCurrentUser();
    final chats = await _chatService.getChats();
    if (mounted) {
      setState(() {
        _user = user;
        _chats = chats;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/chats') return;
    if (route == '/seller/settings') {
      final authId = Provider.of<AuthProvider>(context, listen: false).user?.id;
      Navigator.pushNamed(context, route, arguments: _user?.id ?? authId);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Kotak Masuk Chat'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/chats',
        onNavigate: _handleNavigation,
        onLogout: () async {
          await _authService.logout();
          if (mounted) Navigator.pushReplacementNamed(context, '/login');
        },
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
        : RefreshIndicator(
            onRefresh: _loadData,
            child: _chats.isEmpty 
              ? _buildEmptyState()
              : ListView.separated(
                  padding: const EdgeInsets.all(20),
                  itemCount: _chats.length,
                  separatorBuilder: (context, index) => const SizedBox(height: 12),
                  itemBuilder: (context, index) => _buildChatItem(_chats[index]),
                ),
          ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.comments_disabled_outlined, size: 80, color: Colors.grey.shade300),
          const SizedBox(height: 20),
          Text('Belum ada obrolan.', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
          const SizedBox(height: 8),
          Text('Tunggu pesan dari calon pembeli barang Anda.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildChatItem(Chat chat) {
    final partner = chat.user1Id == _user?.id ? chat.user2 : chat.user1;
    final lastMsg = chat.messages.isNotEmpty ? chat.messages.last : null;
    final isUnread = lastMsg != null && !lastMsg.isRead && lastMsg.senderId != _user?.id;

    return InkWell(
      onTap: () async {
        await Navigator.pushNamed(context, '/chat/room', arguments: chat);
        _loadData();
      },
      borderRadius: BorderRadius.circular(15),
      child: Container(
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(15),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
        ),
        child: Row(
          children: [
            // Avatar
            Container(
              width: 55,
              height: 55,
              decoration: BoxDecoration(color: const Color(0xFF9F1521).withOpacity(0.05), shape: BoxShape.circle, border: Border.all(color: const Color(0xFF9F1521).withOpacity(0.1))),
              child: const Icon(Icons.person, color: Color(0xFF9F1521), size: 30),
            ),
            const SizedBox(width: 15),
            // Preview
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(partner?.name ?? 'Pengguna Telcopedia', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 14)),
                      Text(lastMsg != null ? DateFormat('HH:mm').format(lastMsg.createdAt) : '', style: GoogleFonts.plusJakartaSans(fontSize: 9, color: Colors.grey)),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      if (lastMsg?.senderId == _user?.id)
                        const Padding(padding: EdgeInsets.only(right: 4), child: Icon(Icons.done_all, size: 14, color: Colors.blue)),
                      Expanded(
                        child: Text(
                          lastMsg?.message ?? 'Mulai obrolan sekarang...',
                          style: GoogleFonts.plusJakartaSans(fontSize: 12, color: isUnread ? Colors.black : Colors.grey.shade600, fontWeight: isUnread ? FontWeight.bold : FontWeight.normal),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      if (isUnread)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(color: Colors.red, borderRadius: BorderRadius.circular(10)),
                          child: Text('BARU', style: GoogleFonts.plusJakartaSans(fontSize: 7, color: Colors.white, fontWeight: FontWeight.bold)),
                        ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(width: 10),
            Icon(Icons.chevron_right, color: Colors.grey.shade300, size: 18),
          ],
        ),
      ),
    );
  }
}
