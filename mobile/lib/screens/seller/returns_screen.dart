import 'package:flutter/material.dart';
import '../../models/product_return.dart';
import '../../models/user.dart';
import '../../services/seller_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class SellerReturnsScreen extends StatefulWidget {
  const SellerReturnsScreen({super.key});

  @override
  State<SellerReturnsScreen> createState() => _SellerReturnsScreenState();
}

class _SellerReturnsScreenState extends State<SellerReturnsScreen> {
  final SellerService _sellerService = SellerService();
  final AuthService _authService = AuthService();
  List<ProductReturn> _returns = [];
  User? _user;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final user = await _authService.getCurrentUser();
    final returns = await _sellerService.getMyReturns();
    if (mounted) {
      setState(() {
        _user = user;
        _returns = returns;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/returns') return;
    if (route == '/seller/settings') {
      final authId = Provider.of<AuthProvider>(context, listen: false).user?.id;
      Navigator.pushNamed(context, route, arguments: _user?.id ?? authId);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Retur & Komplain'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/returns',
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
                  _buildHeader(),
                  const SizedBox(height: 25),
                  _buildReturnsList(),
                  const SizedBox(height: 50),
                ],
              ),
            ),
          ),
    );
  }

  Widget _buildHeader() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Pengajuan Retur', style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900)),
        Text('Kelola keluhan dan permintaan pengembalian barang.', style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600)),
      ],
    );
  }

  Widget _buildReturnsList() {
    if (_returns.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 80),
          child: Column(
            children: [
              Icon(Icons.assignment_return_outlined, size: 60, color: Colors.grey.shade300),
              const SizedBox(height: 15),
              Text('Tidak ada pengajuan retur.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
              Text('Bagus! Semua pembeli puas.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12)),
            ],
          ),
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _returns.length,
        separatorBuilder: (context, index) => Divider(color: Colors.grey.shade100, height: 1),
        itemBuilder: (context, index) {
          final ret = _returns[index];
          return _buildReturnItem(ret);
        },
      ),
    );
  }

  Widget _buildReturnItem(ProductReturn ret) {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Product Image
              ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: Image.network(
                  ret.product?.imageUrl ?? 'https://via.placeholder.com/150',
                  width: 55,
                  height: 55,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => Container(width: 55, height: 55, color: Colors.grey.shade100, child: const Icon(Icons.image_not_supported_outlined, size: 18)),
                ),
              ),
              const SizedBox(width: 15),
              // Info
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(ret.product?.name ?? 'Produk Tidak Diketahui', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13), maxLines: 1, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 4),
                    Text('ORDER: #TPD-${ret.orderId}', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
              _buildStatusBadge(ret.status),
            ],
          ),
          const SizedBox(height: 15),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(ret.user?.name ?? 'Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold)),
                  Text('ID: #${ret.userId}', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
                ],
              ),
              OutlinedButton(
                onPressed: () => _showReturnDetail(ret),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Color(0xFF1A1A1A)),
                  foregroundColor: const Color(0xFF1A1A1A),
                  padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 0),
                  minimumSize: const Size(0, 32),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                ),
                child: Text('Lihat Detail', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color color;
    Color bgColor;
    String label;

    switch (status) {
      case 'pending':
        color = Colors.orange;
        bgColor = Colors.orange.shade50;
        label = 'MENUNGGU';
        break;
      case 'approved':
        color = Colors.green;
        bgColor = Colors.green.shade50;
        label = 'DISETUJUI';
        break;
      case 'rejected':
        color = Colors.red;
        bgColor = Colors.red.shade50;
        label = 'DITOLAK';
        break;
      default:
        color = Colors.grey;
        bgColor = Colors.grey.shade50;
        label = status.toUpperCase();
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(6), border: Border.all(color: color.withOpacity(0.3))),
      child: Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  void _showReturnDetail(ProductReturn ret) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(25))),
        padding: const EdgeInsets.all(25),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 20),
              Text('Detail Pengajuan Retur', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
              const SizedBox(height: 20),
              
              // Product Summary
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200, style: BorderStyle.solid)),
                child: Row(
                  children: [
                    ClipRRect(borderRadius: BorderRadius.circular(10), child: Image.network(ret.product?.imageUrl ?? '', width: 60, height: 60, fit: BoxFit.cover, errorBuilder: (c, e, s) => Container(width: 60, height: 60, color: Colors.white))),
                    const SizedBox(width: 15),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(ret.product?.name ?? '-', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
                          Text('ID PESANAN: #TPD-${ret.orderId}', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 25),
              
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text('Diajukan Oleh', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                    Text(ret.user?.name ?? '-', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: const Color(0xFF1A1A1A))),
                  ]),
                  Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
                    Text('Status Saat Ini', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                    _buildStatusBadge(ret.status),
                  ]),
                ],
              ),
              
              const SizedBox(height: 25),
              Text('Alasan Pengembalian', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
              const SizedBox(height: 8),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                child: Text(ret.reason, style: GoogleFonts.plusJakartaSans(fontSize: 12, height: 1.6)),
              ),
              
              if (ret.media != null) ...[
                const SizedBox(height: 25),
                Text('Lampiran Bukti', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                const SizedBox(height: 8),
                ClipRRect(
                  borderRadius: BorderRadius.circular(15),
                  child: ret.media!.toLowerCase().endsWith('.mp4') || ret.media!.toLowerCase().endsWith('.mov')
                    ? Container(
                        width: double.infinity,
                        height: 200,
                        color: Colors.black,
                        child: const Center(child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.play_circle_outline, color: Colors.white, size: 50), SizedBox(height: 10), Text('Video Bukti', style: TextStyle(color: Colors.white, fontSize: 12))])),
                      )
                    : Image.network(
                        ret.media!,
                        width: double.infinity,
                        height: 200,
                        fit: BoxFit.cover,
                      ),
                ),
              ],
              
              const SizedBox(height: 30),
              
              if (ret.status == 'pending')
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () async {
                          final success = await _sellerService.approveReturn(ret.id);
                          if (success && mounted) {
                            Navigator.pop(context);
                            _loadData();
                          }
                        },
                        style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), padding: const EdgeInsets.symmetric(vertical: 15)),
                        child: Text('Setujui Retur', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () async {
                          final success = await _sellerService.rejectReturn(ret.id);
                          if (success && mounted) {
                            Navigator.pop(context);
                            _loadData();
                          }
                        },
                        style: OutlinedButton.styleFrom(foregroundColor: Colors.black, side: const BorderSide(color: Colors.black), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), padding: const EdgeInsets.symmetric(vertical: 15)),
                        child: const Text('Tolak'),
                      ),
                    ),
                  ],
                )
              else
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(15),
                  decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15)),
                  child: Center(child: Text('KEPUTUSAN TELAH DIAMBIL PADA ${DateFormat('dd MMM YYYY').format(ret.updatedAt).toUpperCase()}', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey))),
                ),
              
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }
}
