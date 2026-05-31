import 'package:flutter/material.dart';
import '../../models/penarikan_dana.dart';
import '../../services/admin_service.dart';
import '../../services/auth_service.dart';
import '../../models/user.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../widgets/admin_sidebar.dart';
import 'dashboard_screen.dart';
import 'products_screen.dart';
import 'users_screen.dart';
import 'vouchers_screen.dart';
import 'payments_screen.dart';

class AdminPenarikanDanaScreen extends StatefulWidget {
  const AdminPenarikanDanaScreen({super.key});

  @override
  State<AdminPenarikanDanaScreen> createState() => _AdminPenarikanDanaScreenState();
}

class _AdminPenarikanDanaScreenState extends State<AdminPenarikanDanaScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  List<PenarikanDana> _penarikan = [];
  User? _user;
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
        _adminService.getAllPenarikanDanas(),
        _authService.getCurrentUser(),
      ]);
      
      if (mounted) {
        setState(() {
          _penarikan = results[0] as List<PenarikanDana>? ?? [];
          _user = results[1] as User?;
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
      case '/admin/users': screen = const AdminUsersScreen(); break;
      case '/admin/vouchers': screen = const AdminVouchersScreen(); break;
      case '/admin/payments': screen = const AdminPaymentsScreen(); break;
      case '/admin/penarikan': return;
      default: return;
    }
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => screen));
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Persetujuan Dana'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _user,
        currentRoute: '/admin/penarikan',
        pendingPenarikanDana: _penarikan.where((w) => w.status == 'pending').length,
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
                  _buildPenarikanDanasList(currencyFormatter),
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
          'Persetujuan Dana',
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        Text(
          'Tinjau dan proses permintaan penarikan dana dari para seller.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildPenarikanDanasList(NumberFormat formatter) {
    if (_penarikan.isEmpty) {
      return Center(
        child: Column(
          children: [
            const SizedBox(height: 50),
            Icon(Icons.account_balance_outlined, size: 80, color: Colors.grey.withValues(alpha: 0.2)),
            const SizedBox(height: 20),
            Text('Belum ada permintaan penarikan dana.', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey)),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 4))]),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _penarikan.length,
        separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF1F1F1)),
        itemBuilder: (context, index) {
          final w = _penarikan[index];
          return Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(w.user?.name ?? 'Seller', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
                          Text(w.user?.email ?? '', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                        ],
                      ),
                    ),
                    _buildStatusBadge(w.status),
                  ],
                ),
                const SizedBox(height: 15),
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('JUMLAH PENCAIRAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                          Text(formatter.format(w.amount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 16, color: const Color(0xFF9F1521))),
                        ],
                      ),
                    ),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('TANGGAL', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                          Text(DateFormat('dd MMM, HH:mm').format(w.createdAt), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 12)),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(8), border: Border.all(color: Colors.grey.shade200)),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('REKENING TUJUAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                      const SizedBox(height: 4),
                      Text(w.bankName, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 12)),
                      Text('${w.accountNumber} a/n ${w.accountName}', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700)),
                    ],
                  ),
                ),
                if (w.status == 'pending') ...[
                  const SizedBox(height: 15),
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => _handleAction(w.id, 'approve'),
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.green, foregroundColor: Colors.white, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
                          child: const Text('Approve', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => _handleAction(w.id, 'reject'),
                          style: OutlinedButton.styleFrom(foregroundColor: Colors.red, side: const BorderSide(color: Colors.red), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
                          child: const Text('Reject', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color = Colors.orange;
    String text = 'PENDING';
    if (status == 'approved') { color = Colors.green; text = 'DICAIRKAN'; }
    else if (status == 'rejected') { color = Colors.red; text = 'DITOLAK'; }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(4), border: Border.all(color: color.withValues(alpha: 0.5))),
      child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  Future<void> _handleAction(int id, String action) async {
    bool success = false;
    if (action == 'approve') {
      success = await _adminService.approvePenarikanDana(id);
    } else {
      success = await _adminService.rejectPenarikanDana(id);
    }

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Penarikan dana berhasil di-${action == 'approve' ? 'setujui' : 'tolak'}.'), backgroundColor: action == 'approve' ? Colors.green : Colors.red));
      _loadData();
    }
  }
}
