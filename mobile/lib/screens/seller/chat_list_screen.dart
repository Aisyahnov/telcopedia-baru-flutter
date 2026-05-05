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
  final SellerService _sellerService = SellerService();
  final AuthService _authService = AuthService();
  List<dynamic> _chatGroups = [];
  User? _user;
  bool _isLoading = true;
  int? _expandedProductId;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final user = await _authService.getCurrentUser();
    final groups = await _sellerService.getSellerChats();
    if (mounted) {
      setState(() {
        _user = user;
        _chatGroups = groups;
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
            child: _chatGroups.isEmpty 
              ? _buildEmptyState()
              : ListView.separated(
                  padding: const EdgeInsets.all(20),
                  itemCount: _chatGroups.length,
                  separatorBuilder: (context, index) => const SizedBox(height: 15),
                  itemBuilder: (context, index) => _buildProductGroup(_chatGroups[index]),
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

  Widget _buildProductGroup(dynamic group) {
    final product = group['product'];
    final List<dynamic> chats = group['chats'];
    final bool isExpanded = _expandedProductId == product['id'];

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        children: [
          InkWell(
            onTap: () {
              setState(() {
                _expandedProductId = isExpanded ? null : product['id'];
              });
            },
            borderRadius: BorderRadius.circular(20),
            child: Padding(
              padding: const EdgeInsets.all(15),
              child: Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Image.network(
                      product['image_url'] ?? '',
                      width: 50,
                      height: 50,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) => Container(color: Colors.grey.shade200, width: 50, height: 50, child: const Icon(Icons.image)),
                    ),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(product['name'] ?? 'Produk', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14)),
                        Text('${chats.length} Calon Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade600)),
                      ],
                    ),
                  ),
                  Icon(isExpanded ? Icons.keyboard_arrow_up : Icons.keyboard_arrow_down, color: Colors.grey),
                ],
              ),
            ),
          ),
          if (isExpanded)
            Padding(
              padding: const EdgeInsets.only(bottom: 15, left: 15, right: 15),
              child: Column(
                children: chats.map<Widget>((chat) => _buildBuyerChatItem(chat, product)).toList(),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildBuyerChatItem(dynamic chat, dynamic product) {
    final otherUser = chat['other_user'];
    final lastMsg = chat['last_message'];
    
    return InkWell(
      onTap: () async {
        Navigator.pushNamed(context, '/chat/room', arguments: Chat(
          id: chat['id'],
          user1Id: _user!.id, 
          user2Id: otherUser['id'],
          productId: product['id'],
          updatedAt: DateTime.parse(chat['updated_at']),
          createdAt: DateTime.now(),
          user1: _user,
          user2: User.fromJson(otherUser),
          product: Product.fromJson(product),
          messages: [],
        ));
      },
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
        margin: const EdgeInsets.only(bottom: 8),
        decoration: BoxDecoration(
          color: Colors.grey.shade50,
          borderRadius: BorderRadius.circular(15),
        ),
        child: Row(
          children: [
            CircleAvatar(
              radius: 18,
              backgroundColor: const Color(0xFF9F1521).withOpacity(0.1),
              child: Text(
                otherUser['name']?[0].toUpperCase() ?? 'U',
                style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521)),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(otherUser['name'] ?? 'User', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
                  Text(
                    lastMsg?['message'] ?? 'Klik untuk membalas chat...',
                    style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade600),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, size: 16, color: Colors.grey),
          ],
        ),
      ),
    );
  }
}
