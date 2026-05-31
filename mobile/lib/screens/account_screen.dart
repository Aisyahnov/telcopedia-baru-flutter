import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class AccountScreen extends StatelessWidget {
  const AccountScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      body: CustomScrollView(
        slivers: [
          _buildSliverAppBar(user),
          SliverToBoxAdapter(
            child: Column(
              children: [
                const SizedBox(height: 20),
                _buildMenuSection(context, 'Aktivitas Saya', [
                  _buildMenuItem(context, Icons.shopping_bag_outlined, 'Pesanan Saya', '/orders'),
                  _buildMenuItem(context, Icons.favorite_border, 'Wishlist', '/favorites'),
                  _buildMenuItem(context, Icons.chat_outlined, 'Chat', '/chat'),
                ]),
                const SizedBox(height: 20),
                _buildMenuSection(context, 'Pusat Bantuan & Legal', [
                  _buildMenuItem(context, Icons.help_outline, 'Pusat Bantuan (Contact)', '/contact'),
                  _buildMenuItem(context, Icons.info_outline, 'Tentang Telcopedia', '/about'),
                  _buildMenuItem(context, Icons.privacy_tip_outlined, 'Kebijakan Privasi', '/privacy'),
                  _buildMenuItem(context, Icons.description_outlined, 'Syarat & Ketentuan', '/terms'),
                ]),
                const SizedBox(height: 20),
                _buildMenuSection(context, 'Pengaturan', [
                  _buildMenuItem(context, Icons.person_outline, 'Ubah Profil', '/profile'),
                  _buildMenuItem(context, Icons.lock_outline, 'Ubah Password', '/change-password'),
                ]),
                const SizedBox(height: 30),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: ElevatedButton(
                    onPressed: () => _handleLogout(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.white,
                      foregroundColor: const Color(0xFF9F1521),
                      elevation: 0,
                      side: const BorderSide(color: Color(0xFF9F1521)),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      padding: const EdgeInsets.symmetric(vertical: 15),
                      minimumSize: const Size(double.infinity, 50),
                    ),
                    child: Text(
                      'Keluar Akun',
                      style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
                    ),
                  ),
                ),
                const SizedBox(height: 50),
                Text(
                  'Telcopedia v1.0.0',
                  style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 10),
                ),
                const SizedBox(height: 100),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSliverAppBar(dynamic user) {
    return SliverAppBar(
      expandedHeight: 200,
      pinned: true,
      backgroundColor: const Color(0xFF9F1521),
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFF9F1521), Color(0xFF7c111b)],
            ),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const SizedBox(height: 40),
              CircleAvatar(
                radius: 40,
                backgroundColor: Colors.white,
                child: CircleAvatar(
                  radius: 37,
                  backgroundImage: user?.photo != null
                      ? NetworkImage(ApiService.getImageUrl('api/storage/${user!.photo}'))
                      : NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(user?.name ?? "S")}&background=9F1521&color=fff&bold=true') as ImageProvider,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                user?.name ?? 'Mahasiswa Telkom',
                style: GoogleFonts.plusJakartaSans(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 18,
                ),
              ),
              Text(
                user?.email ?? 'telkom.university@student.id',
                style: GoogleFonts.plusJakartaSans(
                  color: Colors.white70,
                  fontSize: 12,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMenuSection(BuildContext context, String title, List<Widget> items) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(left: 20, top: 15, bottom: 5),
            child: Text(
              title,
              style: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w800,
                fontSize: 12,
                color: Colors.grey.shade400,
                letterSpacing: 0.5,
              ),
            ),
          ),
          ...items,
        ],
      ),
    );
  }

  Widget _buildMenuItem(BuildContext context, IconData icon, String title, String route) {
    return ListTile(
      leading: Icon(icon, color: const Color(0xFF9F1521), size: 22),
      title: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: Colors.black87,
        ),
      ),
      trailing: const Icon(Icons.chevron_right, size: 18, color: Colors.grey),
      onTap: () {
        // For legal pages, we could show a WebView or a simple static page
        // For now, we'll navigate if the route exists
        Navigator.pushNamed(context, route);
      },
    );
  }

  Future<void> _handleLogout(BuildContext context) async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Logout', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Text('Apakah Anda yakin ingin keluar?', style: GoogleFonts.plusJakartaSans()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
          TextButton(
            onPressed: () async {
              await authProvider.logout();
              if (context.mounted) {
                Navigator.of(context).pushNamedAndRemoveUntil('/login', (route) => false);
              }
            },
            child: const Text('Keluar', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
