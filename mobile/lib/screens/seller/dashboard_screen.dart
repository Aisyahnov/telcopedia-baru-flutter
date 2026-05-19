import 'package:flutter/material.dart';
import '../../services/auth_service.dart';
import '../../models/user.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:fl_chart/fl_chart.dart';

import '../../services/seller_service.dart';

class SellerDashboardScreen extends StatefulWidget {
  const SellerDashboardScreen({super.key});

  @override
  State<SellerDashboardScreen> createState() => _SellerDashboardScreenState();
}

class _SellerDashboardScreenState extends State<SellerDashboardScreen> {
  final AuthService _authService = AuthService();
  final SellerService _sellerService = SellerService();
  User? _user;
  Map<String, dynamic>? _dashboardStats;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final results = await Future.wait([
      _authService.getCurrentUser(),
      _sellerService.getDashboardStats(),
    ]);
    
    if (mounted) {
      setState(() {
        _user = results[0] as User?;
        _dashboardStats = results[1] as Map<String, dynamic>?;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/dashboard') return;
    if (route == '/seller/settings') {
      Navigator.pushNamed(context, route);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    final dateFormatter = DateFormat('dd MMM yyyy');

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Seller Center'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/dashboard',
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
                  _buildHeader(dateFormatter),
                  const SizedBox(height: 25),
                  
                  if (_user?.isBannedFromPosting ?? false) _buildPenaltyWarning(),
                  
                  _buildStatCards(currencyFormatter),
                  const SizedBox(height: 30),
                  
                  _buildSalesChart(),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildHeader(DateFormat formatter) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Ringkasan Utama',
              style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
            ),
            Text(
              'Statistik performa lapak Anda hari ini.',
              style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
            ),
          ],
        ),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
          child: Row(
            children: [
              Icon(Icons.calendar_today_outlined, size: 12, color: Colors.grey.shade600),
              const SizedBox(width: 6),
              Text(formatter.format(DateTime.now()), style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildPenaltyWarning() {
    return Container(
      margin: const EdgeInsets.only(bottom: 25),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.red.shade50,
        borderRadius: BorderRadius.circular(15),
        border: Border.all(color: Colors.red.withOpacity(0.1)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(color: Colors.red.withOpacity(0.1), shape: BoxShape.circle),
            child: const Icon(Icons.block, color: Colors.red, size: 24),
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Peringatan: Lapak Dibekukan Sebagian', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A), fontSize: 14)),
                const SizedBox(height: 4),
                RichText(
                  text: TextSpan(
                    style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700, height: 1.5),
                    children: [
                      const TextSpan(text: 'Anda telah mencapai batas maksimal poin penalti '),
                      TextSpan(text: '(${_user?.penaltyPoints} dari 3 retur disetujui)', style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.black)),
                      const TextSpan(text: '. Akses Anda untuk memposting produk baru telah diblokir sementara waktu. Harap selesaikan pesanan yang masih berjalan dengan baik.'),
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

  Widget _buildStatCards(NumberFormat formatter) {
    final stats = _dashboardStats;
    return Column(
      children: [
        Row(
          children: [
            Expanded(child: _buildStatCardSmall('PRODUK', '${stats?['total_products'] ?? 0}', Icons.inventory_2_outlined, const Color(0xFF9F1521))),
            const SizedBox(width: 15),
            Expanded(child: _buildStatCardSmall('SALDO', formatter.format(_user?.saldo ?? 0), Icons.wallet_outlined, Colors.green)),
          ],
        ),
        const SizedBox(height: 15),
        Row(
          children: [
            Expanded(child: _buildStatCardSmall('RATING PRODUK', '${stats?['avg_product_rating'] ?? 0.0}', Icons.star_border, Colors.orange)),
            const SizedBox(width: 15),
            Expanded(child: _buildStatCardSmall('RATING SELLER', '${stats?['avg_seller_rating'] ?? 0.0}', Icons.verified_user_outlined, Colors.blue)),
          ],
        ),
      ],
    );
  }

  Widget _buildStatCardSmall(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        border: Border(bottom: BorderSide(color: color, width: 3)),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Icon(icon, color: color, size: 16),
              Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w800, color: Colors.grey.shade600, letterSpacing: 0.5)),
            ],
          ),
          const SizedBox(height: 10),
          Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)), overflow: TextOverflow.ellipsis),
        ],
      ),
    );
  }

  Widget _buildSalesChart() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Grafik Penjualan Mingguan', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A))),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                child: Row(
                  children: [
                    Text('7 Hari Terakhir', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
                    const Icon(Icons.keyboard_arrow_down, size: 14),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 30),
          SizedBox(
            height: 250,
            child: LineChart(
              LineChartData(
                gridData: FlGridData(show: true, drawVerticalLine: false, getDrawingHorizontalLine: (value) => FlLine(color: Colors.grey.shade100, strokeWidth: 1)),
                titlesData: FlTitlesData(
                  show: true,
                  rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                  bottomTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      reservedSize: 30,
                      interval: 1,
                      getTitlesWidget: (value, meta) {
                        const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                        if (value >= 0 && value < days.length) {
                          return Padding(padding: const EdgeInsets.only(top: 10), child: Text(days[value.toInt()], style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 10)));
                        }
                        return const Text('');
                      },
                    ),
                  ),
                  leftTitles: AxisTitles(
                    sideTitles: SideTitles(
                      showTitles: true,
                      interval: 100000,
                      getTitlesWidget: (value, meta) {
                        if (value == 0) return const Text('');
                        return Text('${(value / 1000).toInt()}k', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold, fontSize: 10));
                      },
                      reservedSize: 42,
                    ),
                  ),
                ),
                borderData: FlBorderData(show: false),
                minX: 0,
                maxX: 6,
                minY: 0,
                maxY: 500000,
                lineBarsData: [
                  LineChartBarData(
                    spots: [
                      const FlSpot(0, 120000),
                      const FlSpot(1, 190000),
                      const FlSpot(2, 30000),
                      const FlSpot(3, 50000),
                      const FlSpot(4, 200000),
                      const FlSpot(5, 300000),
                      const FlSpot(6, 450000),
                    ],
                    isCurved: true,
                    gradient: const LinearGradient(colors: [Color(0xFF9F1521), Color(0xFFD32F2F)]),
                    barWidth: 4,
                    isStrokeCapRound: true,
                    dotData: const FlDotData(show: true),
                    belowBarData: BarAreaData(show: true, gradient: LinearGradient(colors: [const Color(0xFF9F1521).withOpacity(0.2), const Color(0xFF9F1521).withOpacity(0.0)])),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
