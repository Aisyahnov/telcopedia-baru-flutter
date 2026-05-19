import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../services/admin_service.dart';
import '../../services/auth_service.dart';
import '../../models/user.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../widgets/admin_sidebar.dart';
import 'dashboard_screen.dart';
import 'users_screen.dart';
import 'vouchers_screen.dart';
import 'payments_screen.dart';
import 'penarikan_screen.dart';

class AdminProductsScreen extends StatefulWidget {
  const AdminProductsScreen({super.key});

  @override
  State<AdminProductsScreen> createState() => _AdminProductsScreenState();
}

class _AdminProductsScreenState extends State<AdminProductsScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  List<Product> _products = [];
  User? _user;
  bool _isLoading = true;
  String _successMessage = '';
  bool _showSuccess = false;

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
        _adminService.getAllProducts(),
        _authService.getCurrentUser(),
      ]);
      
      if (mounted) {
        setState(() {
          _products = results[0] as List<Product>? ?? [];
          _user = results[1] as User?;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _triggerSuccess(String message) {
    setState(() {
      _successMessage = message;
      _showSuccess = true;
    });
    Future.delayed(const Duration(seconds: 4), () {
      if (mounted) setState(() => _showSuccess = false);
    });
  }

  void _handleNavigation(String route) {
    Widget screen;
    switch (route) {
      case '/admin/dashboard': screen = const AdminDashboardScreen(); break;
      case '/admin/products': return;
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
        title: const Text('Screening Produk'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _user,
        currentRoute: '/admin/products',
        pendingProducts: _products.where((p) => p.status == 'pending').length,
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
                  const SizedBox(height: 20),
                  if (_showSuccess) _buildSuccessAlert(),
                  const SizedBox(height: 10),
                  _buildProductsList(currencyFormatter),
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
          'Screening Produk',
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        Text(
          'Audit dan takedown produk yang melanggar ketentuan kampus.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildSuccessAlert() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: Colors.green.withOpacity(0.1), shape: BoxShape.circle),
            child: const Icon(Icons.check_circle, color: Colors.green, size: 24),
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Berhasil!', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14)),
                Text(_successMessage, style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade600)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProductsList(NumberFormat formatter) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _products.length,
        separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF1F1F1)),
        itemBuilder: (context, index) {
          final p = _products[index];
          return Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Product Image
                    Stack(
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(12),
                          child: Image.network(
                            p.imageUrl ?? '',
                            width: 55,
                            height: 55,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) => Container(width: 55, height: 55, color: Colors.grey.shade200, child: const Icon(Icons.image_not_supported)),
                          ),
                        ),
                        if (p.status == 'pending')
                          Positioned(
                            top: 0,
                            right: 0,
                            child: Container(
                              width: 12,
                              height: 12,
                              decoration: BoxDecoration(color: Colors.orange, shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2)),
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(width: 15),
                    // Info
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(p.name, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14, color: const Color(0xFF1A1A1A))),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              _buildTinyBadge(p.category?.name ?? 'UNCATEGORIZED', Colors.grey.shade100, Colors.grey.shade600),
                              const SizedBox(width: 6),
                              _buildStatusBadge(p.status),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                // Seller & Price Row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.storefront, size: 12, color: Color(0xFF9F1521)),
                            const SizedBox(width: 4),
                            Text(p.seller?.name ?? 'No Seller', style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold)),
                          ],
                        ),
                        Text('ID: #${p.sellerId}', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(formatter.format(p.price), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14, color: const Color(0xFF9F1521))),
                        Text('STOCK: ${p.stock}', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 15),
                // Actions
                Row(
                  children: [
                    if (p.status == 'pending' || p.status == 'rejected' || p.status == 'inactive') ...[
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => _moderate(p.id, 'approve'),
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.green, foregroundColor: Colors.white, elevation: 0, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
                          child: const Text('Setujui', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                      ),
                      const SizedBox(width: 8),
                    ],
                    if (p.status == 'pending' || p.status == 'approved' || p.status == 'active') ...[
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => _moderate(p.id, 'reject'),
                          style: OutlinedButton.styleFrom(foregroundColor: Colors.red, side: const BorderSide(color: Colors.red), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20))),
                          child: const Text('Tolak', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                      ),
                      const SizedBox(width: 8),
                    ],
                    IconButton(
                      onPressed: () => _showProductDetail(p, formatter),
                      icon: const Icon(Icons.visibility_outlined, color: Colors.blue),
                      style: IconButton.styleFrom(backgroundColor: Colors.blue.withOpacity(0.05)),
                    ),
                    IconButton(
                      onPressed: () => _moderate(p.id, 'delete'),
                      icon: const Icon(Icons.delete_outline, color: Colors.red),
                      style: IconButton.styleFrom(backgroundColor: Colors.red.withOpacity(0.05)),
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

  void _showProductDetail(Product p, NumberFormat formatter) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: MediaQuery.of(context).size.height * 0.85,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(30)),
        ),
        child: Column(
          children: [
            Container(
              margin: const EdgeInsets.symmetric(vertical: 15),
              width: 50,
              height: 5,
              decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(10)),
            ),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(25),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(20),
                      child: Image.network(
                        p.imageUrl ?? '',
                        width: double.infinity,
                        height: 250,
                        fit: BoxFit.cover,
                      ),
                    ),
                    const SizedBox(height: 20),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        _buildTinyBadge(p.category?.name ?? 'UNCATEGORIZED', Colors.grey.shade100, Colors.grey.shade600),
                        _buildStatusBadge(p.status),
                      ],
                    ),
                    const SizedBox(height: 15),
                    Text(p.name, style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900)),
                    Text(formatter.format(p.price), style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                    const SizedBox(height: 20),
                    Container(
                      padding: const EdgeInsets.all(15),
                      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                      child: Row(
                        children: [
                          Expanded(child: _buildDetailInfo('KONDISI', p.condition.toUpperCase())),
                          Container(width: 1, height: 30, color: Colors.grey.shade300),
                          Expanded(child: _buildDetailInfo('STOK', '${p.stock} Unit')),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
                    Text('DESKRIPSI', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey)),
                    const SizedBox(height: 8),
                    Text(p.description, style: GoogleFonts.plusJakartaSans(fontSize: 14, color: Colors.grey.shade800, height: 1.6)),
                    const SizedBox(height: 25),
                    Text('PENJUAL', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey)),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        CircleAvatar(
                          backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(p.seller?.name ?? "S")}&background=9F1521&color=fff'),
                          radius: 20,
                        ),
                        const SizedBox(width: 12),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(p.seller?.name ?? 'Unknown', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14)),
                            Text(p.seller?.nim ?? '-', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey)),
                          ],
                        ),
                      ],
                    ),
                    const SizedBox(height: 40),
                  ],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(20),
              child: Row(
                children: [
                  if (p.status == 'pending' || p.status == 'rejected')
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          Navigator.pop(context);
                          _moderate(p.id, 'approve');
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 15),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
                        ),
                        child: const Text('Setujui Produk', style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ),
                  if (p.status == 'pending' || p.status == 'approved' || p.status == 'active') ...[
                    if (p.status == 'pending' || p.status == 'rejected') const SizedBox(width: 10),
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () {
                          Navigator.pop(context);
                          _moderate(p.id, 'reject');
                        },
                        style: OutlinedButton.styleFrom(
                          foregroundColor: Colors.red,
                          side: const BorderSide(color: Colors.red),
                          padding: const EdgeInsets.symmetric(vertical: 15),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
                        ),
                        child: const Text('Tolak Produk', style: TextStyle(fontWeight: FontWeight.bold)),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailInfo(String label, String value) {
    return Column(
      children: [
        Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 8, color: Colors.grey, fontWeight: FontWeight.bold)),
        const SizedBox(height: 4),
        Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
      ],
    );
  }

  Widget _buildTinyBadge(String text, Color bg, Color textCol) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(4), border: Border.all(color: Colors.grey.shade300)),
      child: Text(text.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 7, fontWeight: FontWeight.w800, color: textCol)),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color = Colors.grey;
    String text = status.toUpperCase();

    if (status == 'pending') { color = Colors.orange; text = 'WAITING REVIEW'; }
    else if (status == 'approved' || status == 'active') { color = Colors.green; text = 'LIVE'; }
    else if (status == 'rejected' || status == 'inactive') { color = Colors.red; text = 'REJECTED'; }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(4), border: Border.all(color: color.withOpacity(0.5))),
      child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 7, fontWeight: FontWeight.w900, color: color)),
    );
  }

  Future<void> _moderate(int id, String action) async {
    bool success = false;
    String msg = '';
    
    if (action == 'approve') {
      success = await _adminService.approveProduct(id);
      msg = 'Produk berhasil disetujui untuk tayang!';
    } else if (action == 'reject') {
      success = await _adminService.rejectProduct(id);
      msg = 'Produk telah ditolak.';
    } else if (action == 'delete') {
      final confirm = await showDialog<bool>(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Takedown Produk?'),
          content: const Text('Produk akan dihapus permanen dari sistem.'),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
            TextButton(onPressed: () => Navigator.pop(context, true), child: const Text('Takedown', style: TextStyle(color: Colors.red))),
          ],
        ),
      );
      if (confirm == true) {
        success = await _adminService.deleteProduct(id);
        msg = 'Produk berhasil dihapus dari sistem.';
      } else { return; }
    }

    if (success) {
      _triggerSuccess(msg);
      _loadData();
    }
  }
}
