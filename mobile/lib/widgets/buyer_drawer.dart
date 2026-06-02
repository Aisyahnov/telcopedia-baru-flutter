import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../services/auth_service.dart';

class BuyerDrawer extends StatelessWidget {
  const BuyerDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;
    final authService = AuthService();

    return Drawer(
      backgroundColor: Colors.white,
      child: Column(
        children: [
          // Header
          UserAccountsDrawerHeader(
            decoration: const BoxDecoration(
              color: Color(0xFF9F1521),
            ),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              backgroundImage: user?.photo != null
                  ? NetworkImage(ApiService.getImageUrl('storage/${user!.photo}'))
                  : NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(user?.name ?? "")}&background=fff&color=9F1521&bold=true') as ImageProvider,
            ),
            accountName: Text(
              user?.name ?? 'Guest',
              style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            accountEmail: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.2),
                borderRadius: BorderRadius.circular(100),
              ),
              child: Text(
                '${user?.role.toUpperCase()} ACCOUNT',
                style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w800),
              ),
            ),
          ),

          // Menu Items
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                if (user?.role == 'admin')
                  _buildMenuItem(
                    context,
                    Icons.admin_panel_settings_outlined,
                    'Dashboard Control',
                    () => Navigator.pushReplacementNamed(context, '/admin/dashboard'),
                  ),
                if (user?.role == 'seller')
                  _buildMenuItem(
                    context,
                    Icons.storefront_outlined,
                    'Kelola Toko',
                    () => Navigator.pushReplacementNamed(context, '/seller/dashboard'),
                  ),
                
                _buildSectionHeader('AKTIVITAS SAYA'),
                _buildMenuItem(
                  context,
                  Icons.history_outlined,
                  'Riwayat Belanja',
                  () => Navigator.pushNamed(context, '/orders'),
                ),
                _buildMenuItem(
                  context,
                  Icons.favorite_border_outlined,
                  'Wishlist / Favorit',
                  () => Navigator.pushNamed(context, '/favorites'),
                ),
                _buildMenuItem(
                  context,
                  Icons.confirmation_number_outlined,
                  'Voucher Saya',
                  () => Navigator.pushNamed(context, '/vouchers'),
                ),

                _buildSectionHeader('PENGATURAN'),
                _buildMenuItem(
                  context,
                  Icons.person_outline,
                  'Pengaturan Akun',
                  () => Navigator.pushNamed(context, '/profile'),
                ),
                _buildMenuItem(
                  context,
                  Icons.help_outline,
                  'Pusat Bantuan',
                  () {},
                ),
              ],
            ),
          ),

          // Logout
          Padding(
            padding: const EdgeInsets.all(20),
            child: OutlinedButton(
              onPressed: () {
                showDialog(
                  context: context,
                  builder: (context) => AlertDialog(
                    title: Text('Keluar Akun', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                    content: Text('Apakah Anda yakin ingin keluar?', style: GoogleFonts.plusJakartaSans()),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
                      TextButton(
                        onPressed: () async {
                          Navigator.pop(context); // close dialog
                          await authService.logout();
                          if (context.mounted) Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
                        },
                        child: const Text('Keluar', style: TextStyle(color: Colors.red)),
                      ),
                    ],
                  ),
                );
              },
              style: OutlinedButton.styleFrom(
                side: const BorderSide(color: Color(0xFF9F1521)),
                foregroundColor: const Color(0xFF9F1521),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
                minimumSize: const Size(double.infinity, 45),
              ),
              child: Text(
                'KELUAR',
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 20, top: 20, bottom: 10),
      child: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 11,
          fontWeight: FontWeight.w800,
          color: Colors.grey.shade400,
          letterSpacing: 1,
        ),
      ),
    );
  }

  Widget _buildMenuItem(BuildContext context, IconData icon, String title, VoidCallback onTap) {
    return ListTile(
      leading: Icon(icon, color: Colors.black87, size: 22),
      title: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: Colors.black87,
        ),
      ),
      onTap: () {
        Navigator.pop(context); // Close drawer
        onTap();
      },
      contentPadding: const EdgeInsets.symmetric(horizontal: 20),
      visualDensity: VisualDensity.compact,
    );
  }
}
