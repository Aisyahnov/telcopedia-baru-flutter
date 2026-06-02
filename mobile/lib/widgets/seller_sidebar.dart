import 'package:flutter/material.dart';
import '../../models/user.dart';
import 'package:google_fonts/google_fonts.dart';

class SellerSidebar extends StatelessWidget {
  final User? user;
  final String currentRoute;
  final Function(String) onNavigate;
  final VoidCallback onLogout;

  const SellerSidebar({
    super.key,
    required this.user,
    required this.currentRoute,
    required this.onNavigate,
    required this.onLogout,
  });

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: Container(
        color: Colors.white,
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 10),
                children: [
                  _buildSectionHeader('MENU UTAMA'),
                  _buildMenuItem(context, Icons.pie_chart_outline, 'Ringkasan Utama', '/seller/dashboard'),
                  _buildMenuItem(context, Icons.inventory_2_outlined, 'Kelola Produk', '/seller/products'),
                  _buildMenuItem(context, Icons.receipt_long_outlined, 'Kelola Pesanan', '/seller/orders'),
                  _buildMenuItem(context, Icons.rotate_left_outlined, 'Retur & Komplain', '/seller/returns'),
                  
                  const SizedBox(height: 20),
                  _buildSectionHeader('KOMUNIKASI'),
                  _buildMenuItem(context, Icons.chat_bubble_outline, 'Chat Pembeli', '/seller/chats'),

                  const SizedBox(height: 20),
                  _buildSectionHeader('PENGATURAN'),
                  _buildMenuItem(context, Icons.wallet_outlined, 'Saldo & Penarikan', '/seller/penarikan'),
                  _buildMenuItem(context, Icons.settings_outlined, 'Pengaturan Toko', '/seller/settings'),
                ],
              ),
            ),
            _buildFooter(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 60, 20, 30),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(bottom: BorderSide(color: Color(0xFFF1F1F1))),
      ),
      child: Column(
        children: [
          // Logo Telcopedia
          Icon(Icons.shopping_bag, size: 40, color: const Color(0xFF9F1521)),
          const SizedBox(height: 10),
          Text('TELCOPEDIA', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 18, color: const Color(0xFF9F1521))),
          const SizedBox(height: 25),
          
          // User Profile Card
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(15),
              border: Border.all(color: const Color(0xFFF1F1F1)),
              boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10, offset: const Offset(0, 4))],
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 18,
                  backgroundColor: const Color(0xFF9F1521),
                  backgroundImage: user?.photo != null 
                    ? NetworkImage(user!.photo!) 
                    : null,
                  child: user?.photo == null 
                    ? Text(user?.name.substring(0, 1).toUpperCase() ?? 'S', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12))
                    : null,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(user?.name ?? 'Seller', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A)), overflow: TextOverflow.ellipsis),
                      Text(user?.role.toUpperCase() ?? 'SELLER', style: GoogleFonts.plusJakartaSans(fontSize: 9, color: Colors.grey.shade500, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 10, bottom: 10, top: 10),
      child: Text(title, style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.grey.shade400, letterSpacing: 1.5)),
    );
  }

  Widget _buildMenuItem(BuildContext context, IconData icon, String title, String route) {
    bool isActive = currentRoute == route;
    return Container(
      margin: const EdgeInsets.only(bottom: 5),
      child: ListTile(
        onTap: () {
          Navigator.pop(context); // Close drawer first
          onNavigate(route);
        },
        leading: Icon(icon, size: 20, color: isActive ? const Color(0xFF9F1521) : Colors.grey.shade600),
        title: Text(title, style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: isActive ? FontWeight.w800 : FontWeight.w600, color: isActive ? const Color(0xFF9F1521) : Colors.grey.shade700)),
        trailing: isActive ? Container(width: 4, height: 4, decoration: const BoxDecoration(color: Color(0xFF9F1521), shape: BoxShape.circle)) : null,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        tileColor: isActive ? const Color(0xFF9F1521).withValues(alpha: 0.05) : Colors.transparent,
        dense: true,
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Color(0xFFF8F9FA),
        border: Border(top: BorderSide(color: Color(0xFFF1F1F1))),
      ),
      child: SizedBox(
        width: double.infinity,
        child: ElevatedButton.icon(
        onPressed: () {
          showDialog(
            context: context,
            builder: (context) => AlertDialog(
              title: Text('Keluar Akun', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
              content: Text('Apakah Anda yakin ingin keluar?', style: GoogleFonts.plusJakartaSans()),
              actions: [
                TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
                TextButton(
                  onPressed: () {
                    Navigator.pop(context);
                    onLogout();
                  },
                  child: const Text('Keluar', style: TextStyle(color: Colors.red)),
                ),
              ],
            ),
          );
        },
          icon: const Icon(Icons.logout, size: 16),
          label: const Text('KELUAR'),
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF9F1521),
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 12),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
            elevation: 2,
            textStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 12),
          ),
        ),
      ),
    );
  }
}
