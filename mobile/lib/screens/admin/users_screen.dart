import 'package:flutter/material.dart';
import '../../models/user.dart';
import '../../services/admin_service.dart';
import '../../services/auth_service.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../widgets/admin_sidebar.dart';
import 'dashboard_screen.dart';
import 'products_screen.dart';
import 'vouchers_screen.dart';
import 'payments_screen.dart';
import 'withdrawals_screen.dart';

class AdminUsersScreen extends StatefulWidget {
  const AdminUsersScreen({super.key});

  @override
  State<AdminUsersScreen> createState() => _AdminUsersScreenState();
}

class _AdminUsersScreenState extends State<AdminUsersScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  List<User> _users = [];
  User? _currentUser;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    if (!mounted) return;
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        _adminService.getAllUsers(),
        _authService.getCurrentUser(),
      ]);
      
      if (mounted) {
        setState(() {
          _users = results[0] as List<User>? ?? [];
          _currentUser = results[1] as User?;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _handleNavigation(String route) {
    Widget screen;
    switch (route) {
      case '/admin/dashboard': screen = const AdminDashboardScreen(); break;
      case '/admin/products': screen = const AdminProductsScreen(); break;
      case '/admin/users': return;
      case '/admin/vouchers': screen = const AdminVouchersScreen(); break;
      case '/admin/payments': screen = const AdminPaymentsScreen(); break;
      case '/admin/withdrawals': screen = const AdminWithdrawalsScreen(); break;
      default: return;
    }
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => screen));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Kelola User'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _currentUser,
        currentRoute: '/admin/users',
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
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildHero(),
                  const SizedBox(height: 25),
                  _buildUsersList(),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildHero() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Monitoring User',
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        Text(
          'Kendalikan akses dan moderasi akun mahasiswa Telkom.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
        ),
        const SizedBox(height: 10),
        Text(
          'Total ${_users.length} mahasiswa terdaftar.',
          style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521)),
        ),
      ],
    );
  }

  Widget _buildUsersList() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _users.length,
        separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF1F1F1)),
        itemBuilder: (context, index) {
          final u = _users[index];
          final isMe = u.id == _currentUser?.id;
          final initial = u.name.isNotEmpty ? u.name[0].toUpperCase() : '?';

          return Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              children: [
                // Initial Avatar
                Container(
                  width: 45,
                  height: 45,
                  decoration: BoxDecoration(color: Colors.grey.shade100, shape: BoxShape.circle, border: Border.all(color: Colors.grey.shade300)),
                  child: Center(
                    child: Text(initial, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 18, color: Colors.grey.shade700)),
                  ),
                ),
                const SizedBox(width: 15),
                // Info
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(u.name.isNotEmpty ? u.name : 'No Name', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14, color: const Color(0xFF1A1A1A))),
                      Text(u.nim ?? 'NIM TIDAK TERSEDIA', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w800, color: Colors.grey.shade500)),
                      Text(u.email, style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade500)),
                    ],
                  ),
                ),
                // Role & Action
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    _buildRoleBadge(u.role),
                    const SizedBox(height: 8),
                    if (!isMe)
                      GestureDetector(
                        onTap: () => _handleBan(u.id),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.red.shade300), boxShadow: [BoxShadow(color: Colors.red.withOpacity(0.1), blurRadius: 4)]),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.block, size: 10, color: Colors.red),
                              const SizedBox(width: 4),
                              Text('Banned', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w900, color: Colors.red)),
                            ],
                          ),
                        ),
                      )
                    else
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade300)),
                        child: Text('MY ACCOUNT', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: Colors.grey.shade500)),
                      ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildRoleBadge(String role) {
    Color bg = Colors.green.shade50;
    Color text = Colors.green;
    if (role == 'admin') { bg = Colors.black; text = Colors.white; }
    else if (role == 'seller') { bg = const Color(0xFF9F1521).withOpacity(0.05); text = const Color(0xFF9F1521); }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(20), border: role == 'admin' ? null : Border.all(color: text.withOpacity(0.5))),
      child: Text(role.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: text)),
    );
  }

  Future<void> _handleBan(int id) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Banned User?'),
        content: const Text('Akses akun mahasiswa ini akan dicabut permanen!'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(context, true), child: const Text('Ya, Banned!', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold))),
        ],
      ),
    );

    if (confirm == true) {
      final success = await _adminService.deleteUser(id);
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('User berhasil di-banned.'), backgroundColor: Colors.red));
        _loadData();
      }
    }
  }
}
