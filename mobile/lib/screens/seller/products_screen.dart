import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../models/user.dart';
import '../../services/seller_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class SellerProductsScreen extends StatefulWidget {
  const SellerProductsScreen({super.key});

  @override
  State<SellerProductsScreen> createState() => _SellerProductsScreenState();
}

class _SellerProductsScreenState extends State<SellerProductsScreen> {
  final SellerService _sellerService = SellerService();
  final AuthService _authService = AuthService();
  List<Product> _products = [];
  User? _user;
  bool _isLoading = true;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final user = await _authService.getCurrentUser();
    final products = await _sellerService.getMyProducts();
    if (mounted) {
      setState(() {
        _user = user;
        _products = products;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/products') return;
    if (route == '/seller/settings') {
      final authId = Provider.of<AuthProvider>(context, listen: false).user?.id;
      Navigator.pushNamed(context, route, arguments: _user?.id ?? authId);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  Future<void> _deleteProduct(Product p) async {
    bool? confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Hapus Produk?', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Text('Data yang dihapus tidak dapat dikembalikan!', style: GoogleFonts.plusJakartaSans()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('BATAL')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
            child: const Text('YA, HAPUS!'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      final success = await _sellerService.deleteProduct(p.id);
      if (!mounted) return;
      if (success) {
        setState(() => _successMessage = 'Produk berhasil dihapus!');
        _loadData();
        Future.delayed(const Duration(seconds: 3), () {
          if (mounted) setState(() => _successMessage = null);
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Daftar Produk Lapak'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/products',
        onNavigate: _handleNavigation,
        onLogout: () async {
          await _authService.logout();
          if (!mounted) return;
          Navigator.pushReplacementNamed(context, '/login');
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
                  _buildHeader(),
                  const SizedBox(height: 25),
                  
                  if (_user?.isBannedFromPosting ?? false) _buildPenaltyWarning(),
                  if (_successMessage != null) _buildSuccessAlert(),
                  
                  _buildProductsList(currencyFormatter),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildHeader() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Daftar Produk', style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900)),
            Text('Total ${_products.length} produk terdaftar.', style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600)),
          ],
        ),
        if (!(_user?.isBannedFromPosting ?? false))
          ElevatedButton.icon(
            onPressed: () => Navigator.pushNamed(context, '/seller/products/create'),
            icon: const Icon(Icons.add_circle_outline, size: 18),
            label: const Text('Tambah Produk'),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF9F1521),
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
            ),
          )
        else
          ElevatedButton.icon(
            onPressed: null,
            icon: const Icon(Icons.block, size: 18),
            label: const Text('Diblokir'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.grey.shade300,
              foregroundColor: Colors.grey.shade600,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
            ),
          ),
      ],
    );
  }

  Widget _buildPenaltyWarning() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.red.withValues(alpha: 0.1))),
      child: Row(
        children: [
          const Icon(Icons.info_outline, color: Colors.red),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              'Lapak Dibekukan. Anda tidak dapat menambah produk baru karena penalti poin.',
              style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.red.shade800, fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSuccessAlert() {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.green.withValues(alpha: 0.1))),
      child: Row(
        children: [
          const Icon(Icons.check_circle_outline, color: Colors.green),
          const SizedBox(width: 12),
          Text(_successMessage!, style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.green.shade800, fontWeight: FontWeight.w600)),
        ],
      ),
    );
  }

  Widget _buildProductsList(NumberFormat formatter) {
    if (_products.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 80),
          child: Column(
            children: [
              Icon(Icons.inventory_2_outlined, size: 60, color: Colors.grey.shade300),
              const SizedBox(height: 15),
              Text('Belum ada produk yang dijual.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
            ],
          ),
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _products.length,
        separatorBuilder: (context, index) => Divider(color: Colors.grey.shade100, height: 1),
        itemBuilder: (context, index) {
          final p = _products[index];
          return _buildProductItem(p, formatter);
        },
      ),
    );
  }

  Widget _buildProductItem(Product p, NumberFormat formatter) {
    return Padding(
      padding: const EdgeInsets.all(15),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Image
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: Image.network(
              p.imageUrl ?? 'https://via.placeholder.com/150',
              width: 60,
              height: 60,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(width: 60, height: 60, color: Colors.grey.shade100, child: const Icon(Icons.image_not_supported_outlined, size: 20)),
            ),
          ),
          const SizedBox(width: 15),
          // Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(p.name, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 14)),
                const SizedBox(height: 4),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(color: const Color(0xFF9F1521).withValues(alpha: 0.05), borderRadius: BorderRadius.circular(4)),
                      child: Text(p.condition.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                    ),
                    if (p.status == 'pending') ...[
                      const SizedBox(width: 8),
                      Text('MENUNGGU VERIFIKASI', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.orange)),
                    ],
                  ],
                ),
                const SizedBox(height: 8),
                Text(formatter.format(p.price), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 13, color: const Color(0xFF9F1521))),
              ],
            ),
          ),
          // Stock & Category
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(20), border: Border.all(color: Colors.grey.shade200)),
                child: Text(p.category?.name.toUpperCase() ?? 'KATEGORI', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
              ),
              const SizedBox(height: 4),
              Text('Stok: ${p.stock}', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
              const SizedBox(height: 10),
              // Actions
              Row(
                children: [
                  _buildCircleAction(Icons.edit_outlined, Colors.blue, () {
                    Navigator.pushNamed(context, '/seller/products/edit', arguments: p);
                  }),
                  const SizedBox(width: 8),
                  _buildCircleAction(Icons.delete_outline, Colors.red, () => _deleteProduct(p)),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCircleAction(IconData icon, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, border: Border.all(color: Colors.grey.shade100), boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 5)]),
        child: Icon(icon, size: 16, color: color),
      ),
    );
  }
}
