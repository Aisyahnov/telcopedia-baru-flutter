import 'package:flutter/material.dart';
import '../../services/auth_service.dart';
import '../../services/admin_service.dart';
import '../../models/user.dart';
import '../../widgets/admin_sidebar.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:fl_chart/fl_chart.dart';
import 'products_screen.dart';
import 'users_screen.dart';
import 'vouchers_screen.dart';
import 'payments_screen.dart';
import 'penarikan_screen.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  Map<String, dynamic> _stats = {};
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
        _adminService.getDashboardStats(),
        _authService.getCurrentUser(),
      ]);
      
      if (mounted) {
        setState(() {
          _stats = results[0] as Map<String, dynamic>? ?? {};
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
      case '/admin/dashboard': return;
      case '/admin/products': screen = const AdminProductsScreen(); break;
      case '/admin/users': screen = const AdminUsersScreen(); break;
      case '/admin/vouchers': screen = const AdminVouchersScreen(); break;
      case '/admin/payments': screen = const AdminPaymentsScreen(); break;
      case '/admin/penarikan': screen = const AdminPenarikanDanaScreen(); break;
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
        title: const Text('Dashboard Admin'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _user,
        currentRoute: '/admin/dashboard',
        pendingProducts: _stats['pending_products'] ?? 0,
        pendingPenarikanDana: _stats['pending_penarikan'] ?? 0,
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
                  const SizedBox(height: 30),
                  _buildStatCards(currencyFormatter),
                  const SizedBox(height: 30),
                  _buildChartCard(),
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
          'Dashboard Admin Telcopedia',
          style: GoogleFonts.plusJakartaSans(fontSize: 24, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        const SizedBox(height: 4),
        Text(
          'Pusat kontrol ekosistem Telcopedia.',
          style: GoogleFonts.plusJakartaSans(fontSize: 14, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildStatCards(NumberFormat formatter) {
    return Column(
      children: [
        _buildStatCard('TOTAL USER', (_stats['total_users'] ?? 0).toString(), Icons.people_outline, Colors.grey.shade800, Colors.grey.shade100),
        const SizedBox(height: 15),
        _buildStatCard('REVENUE ADMIN', formatter.format(_stats['total_revenue'] ?? 0), Icons.monetization_on_outlined, const Color(0xFF9F1521), const Color(0xFF9F1521).withOpacity(0.05)),
        const SizedBox(height: 15),
        _buildStatCard('TOTAL PRODUK', (_stats['total_products'] ?? 0).toString(), Icons.inventory_2_outlined, Colors.green, Colors.green.withOpacity(0.05)),
      ],
    );
  }

  Widget _buildStatCard(String label, String value, IconData icon, Color color, Color bgColor) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        border: Border(bottom: BorderSide(color: color, width: 3)),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Row(
        children: [
          Container(width: 55, height: 55, decoration: BoxDecoration(color: bgColor, shape: BoxShape.circle), child: Icon(icon, color: color, size: 24)),
          const SizedBox(width: 20),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w800, color: Colors.grey.shade600, letterSpacing: 1)),
                Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A))),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildChartCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Grafik Pertumbuhan Transaksi', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14)),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(20)),
                child: Row(children: [Text('30 Hari', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold)), const Icon(Icons.keyboard_arrow_down, size: 12)]),
              ),
            ],
          ),
          const SizedBox(height: 30),
          SizedBox(
            height: 250,
            child: BarChart(
              BarChartData(
                alignment: BarChartAlignment.spaceAround,
                maxY: 120,
                barTouchData: BarTouchData(enabled: false),
                titlesData: FlTitlesData(
                  show: true,
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      getTitlesWidget: (value, meta) {
                        const style = TextStyle(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 10);
                        switch (value.toInt()) {
                          case 0: return const Text('M1', style: style);
                          case 1: return const Text('M2', style: style);
                          case 2: return const Text('M3', style: style);
                          case 3: return const Text('M4', style: style);
                          default: return const Text('');
                        }
                      },
                    ),
                  ),
                  leftTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                ),
                gridData: FlGridData(show: true, drawVerticalLine: false, getDrawingHorizontalLine: (value) => FlLine(color: Colors.grey.shade100, strokeWidth: 1)),
                borderData: FlBorderData(show: false),
                barGroups: [_makeGroupData(0, 45), _makeGroupData(1, 82), _makeGroupData(2, 60), _makeGroupData(3, 110)],
              ),
            ),
          ),
        ],
      ),
    );
  }

  BarChartGroupData _makeGroupData(int x, double y) {
    return BarChartGroupData(
      x: x,
      barRods: [BarChartRodData(toY: y, color: const Color(0xFF9F1521), width: 25, borderRadius: const BorderRadius.vertical(top: Radius.circular(6)))],
    );
  }
}
