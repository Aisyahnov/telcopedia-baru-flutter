import 'package:flutter/material.dart';
import '../../models/user.dart';
import 'package:google_fonts/google_fonts.dart';

class AdminSidebar extends StatelessWidget {
  final User? user;
  final String currentRoute;
  final int pendingProducts;
  final int pendingPenarikanDana;
  final Function(String) onNavigate;
  final VoidCallback onLogout;

  const AdminSidebar({
    super.key,
    required this.user,
    required this.currentRoute,
    this.pendingProducts = 0,
    this.pendingPenarikanDana = 0,
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
                  _buildSectionHeader('OVERVIEW'),
                  _buildMenuItem(Icons.dashboard_outlined, 'Ringkasan Utama', '/admin/dashboard'),
                  _buildMenuItem(Icons.payments_outlined, 'Kelola Pembayaran', '/admin/payments'),
                  _buildMenuItem(Icons.account_balance_wallet_outlined, 'Persetujuan Dana', '/admin/penarikan', badgeCount: pendingPenarikanDana),
                  
                  const SizedBox(height: 20),
                  _buildSectionHeader('MANAGEMENT'),
                  _buildMenuItem(Icons.people_outline, 'Kelola User', '/admin/users'),
                  _buildMenuItem(Icons.inventory_2_outlined, 'Screening Produk', '/admin/products', badgeCount: pendingProducts),
                  _buildMenuItem(Icons.confirmation_number_outlined, 'Kelola Voucher', '/admin/vouchers'),
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
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(15),
              border: Border.all(color: const Color(0xFFF1F1F1)),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4))],
            ),
            child: Row(
              children: [
                CircleAvatar(
                  radius: 18,
                  backgroundColor: const Color(0xFF9F1521),
                  child: Text(user?.name.substring(0, 1).toUpperCase() ?? 'A', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12)),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(user?.name ?? 'Admin', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A)), overflow: TextOverflow.ellipsis),
                      Text('Admin Telcopedia', style: GoogleFonts.plusJakartaSans(fontSize: 9, color: Colors.grey.shade500, fontWeight: FontWeight.bold)),
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

  Widget _buildMenuItem(IconData icon, String title, String route, {int badgeCount = 0}) {
    bool isActive = currentRoute == route;
    return Container(
      margin: const EdgeInsets.only(bottom: 5),
      child: ListTile(
        onTap: () => onNavigate(route),
        leading: Icon(icon, size: 20, color: isActive ? const Color(0xFF9F1521) : Colors.grey.shade600),
        title: Text(title, style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: isActive ? FontWeight.w800 : FontWeight.w600, color: isActive ? const Color(0xFF9F1521) : Colors.grey.shade700)),
        trailing: badgeCount > 0 
          ? Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(color: const Color(0xFF9F1521), borderRadius: BorderRadius.circular(10)),
              child: Text(badgeCount.toString(), style: const TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
            )
          : (isActive ? Container(width: 4, height: 4, decoration: const BoxDecoration(color: Color(0xFF9F1521), shape: BoxShape.circle)) : null),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        tileColor: isActive ? const Color(0xFF9F1521).withOpacity(0.05) : Colors.transparent,
        dense: true,
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(border: Border(top: BorderSide(color: Color(0xFFF1F1F1)))),
      child: TextButton.icon(
        onPressed: onLogout,
        icon: const Icon(Icons.logout, size: 18, color: Colors.red),
        label: Text('Keluar Akun', style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.bold, color: Colors.red)),
        style: TextButton.styleFrom(alignment: Alignment.centerLeft),
      ),
    );
  }
}
