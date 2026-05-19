import 'package:flutter/material.dart';
import '../../models/order.dart';
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
import 'penarikan_screen.dart';

class AdminPaymentsScreen extends StatefulWidget {
  const AdminPaymentsScreen({super.key});

  @override
  State<AdminPaymentsScreen> createState() => _AdminPaymentsScreenState();
}

class _AdminPaymentsScreenState extends State<AdminPaymentsScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  List<Order> _orders = [];
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
        _adminService.getPayments(),
        _authService.getCurrentUser(),
      ]);
      
      if (mounted) {
        setState(() {
          final paymentsData = results[0];
          if (paymentsData is List) {
            _orders = paymentsData.map((json) => Order.fromJson(json)).toList();
          } else {
            _orders = [];
          }
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
      case '/admin/payments': return;
      case '/admin/penarikan': screen = const AdminPenarikanDanaScreen(); break;
      default: return;
    }
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => screen));
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    double totalTransaction = _orders.fold(0, (sum, item) => sum + item.totalAmount);
    double totalAdminFee = _orders.fold(0, (sum, item) => sum + (item.adminFee ?? (item.totalAmount * 0.05)));

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Kelola Pembayaran'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _user,
        currentRoute: '/admin/payments',
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
                  _buildStatsSummary(totalTransaction, totalAdminFee, currencyFormatter),
                  const SizedBox(height: 30),
                  _buildPaymentsList(currencyFormatter),
                  const SizedBox(height: 20),
                  _buildInfoAlert(),
                  const SizedBox(height: 40),
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
          'Monitoring Keuangan',
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        Text(
          'Pantau arus kas, metode pembayaran, dan pendapatan admin fee (5%).',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildStatsSummary(double total, double fee, NumberFormat formatter) {
    return Column(
      children: [
        _buildMiniCard('TOTAL TRANSAKSI', formatter.format(total), Colors.black, Colors.white),
        const SizedBox(height: 12),
        _buildMiniCard('TOTAL BIAYA ADMIN (5%)', formatter.format(fee), const Color(0xFF9F1521), Colors.white, isMaroon: true),
        const SizedBox(height: 12),
        _buildMiniCard('JUMLAH PESANAN', '${_orders.length} Pesanan', Colors.black, Colors.white),
      ],
    );
  }

  Widget _buildMiniCard(String label, String value, Color color, Color bgColor, {bool isMaroon = false}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
        border: isMaroon ? const Border(left: BorderSide(color: Color(0xFF9F1521), width: 4)) : null,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: isMaroon ? const Color(0xFF9F1521) : Colors.grey.shade600)),
          const SizedBox(height: 4),
          Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: isMaroon ? const Color(0xFF9F1521) : color)),
        ],
      ),
    );
  }

  Widget _buildPaymentsList(NumberFormat formatter) {
    if (_orders.isEmpty) {
      return Center(
        child: Column(
          children: [
            const SizedBox(height: 50),
            Icon(Icons.receipt_long_outlined, size: 80, color: Colors.grey.withOpacity(0.2)),
            const SizedBox(height: 20),
            Text('Belum ada transaksi di Telcopedia.', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey)),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _orders.length,
        separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF1F1F1)),
        itemBuilder: (context, index) {
          final order = _orders[index];
          return Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('#TPD-${order.id}', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
                    _buildStatusBadge(order.status),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.person_outline, size: 14, color: Colors.grey),
                    const SizedBox(width: 6),
                    Text(order.user?.name ?? 'Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700)),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('NOMINAL', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text(formatter.format(order.totalAmount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14)),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text('ADMIN FEE', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text(formatter.format(order.adminFee ?? (order.totalAmount * 0.05)), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14, color: const Color(0xFF9F1521))),
                      ],
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

  Widget _buildStatusBadge(String status) {
    Color color = Colors.grey;
    String text = status.toUpperCase();

    if (status == 'completed') { color = Colors.green; text = 'SELESAI'; }
    else if (status == 'paid_verifying') { color = Colors.blue; text = 'DIPERIKSA'; }
    else if (status == 'processing') { color = Colors.orange; text = 'DIPROSES'; }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(4), border: Border.all(color: color.withOpacity(0.5))),
      child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  Widget _buildInfoAlert() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.amber.withOpacity(0.05),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.amber.withOpacity(0.5), style: BorderStyle.solid),
      ),
      child: Row(
        children: [
          const Icon(Icons.info_outline, color: Colors.orange, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              'MONITORING SAJA: Admin memantau transaksi, verifikasi pembayaran dilakukan secara otomatis.',
              style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey.shade700),
            ),
          ),
        ],
      ),
    );
  }
}
