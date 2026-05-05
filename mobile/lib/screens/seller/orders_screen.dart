import 'package:flutter/material.dart';
import '../../models/order.dart';
import '../../models/user.dart';
import '../../services/seller_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class SellerOrdersScreen extends StatefulWidget {
  const SellerOrdersScreen({super.key});

  @override
  State<SellerOrdersScreen> createState() => _SellerOrdersScreenState();
}

class _SellerOrdersScreenState extends State<SellerOrdersScreen> {
  final SellerService _sellerService = SellerService();
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
    final user = await _authService.getCurrentUser();
    final orders = await _sellerService.getMyOrders();
    if (mounted) {
      setState(() {
        _user = user;
        _orders = orders;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/orders') return;
    if (route == '/seller/settings') {
      final authId = Provider.of<AuthProvider>(context, listen: false).user?.id;
      Navigator.pushNamed(context, route, arguments: _user?.id ?? authId);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Manajemen Pesanan'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/orders',
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
                  _buildOrdersList(currencyFormatter),
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
        Text('Pesanan Masuk', style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900)),
        Text('Total ${_orders.length} transaksi yang tercatat.', style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600)),
      ],
    );
  }

  Widget _buildOrdersList(NumberFormat formatter) {
    if (_orders.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 80),
          child: Column(
            children: [
              Icon(Icons.receipt_long_outlined, size: 60, color: Colors.grey.shade300),
              const SizedBox(height: 15),
              Text('Belum ada pesanan masuk.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
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
        itemCount: _orders.length,
        separatorBuilder: (context, index) => Divider(color: Colors.grey.shade100, height: 1),
        itemBuilder: (context, index) {
          final order = _orders[index];
          return _buildOrderItem(order, formatter);
        },
      ),
    );
  }

  Widget _buildOrderItem(Order order, NumberFormat formatter) {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('REFERENCE:', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                  Text('#TPD-${order.id}', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14, color: const Color(0xFF1A1A1A))),
                  Text(DateFormat('dd MMM, HH:mm').format(order.createdAt), style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
                ],
              ),
              _buildStatusBadge(order.status),
            ],
          ),
          const SizedBox(height: 15),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  CircleAvatar(radius: 12, backgroundColor: const Color(0xFF9F1521).withOpacity(0.1), child: const Icon(Icons.person_outline, size: 14, color: Color(0xFF9F1521))),
                  const SizedBox(width: 8),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(order.user?.name ?? 'Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold)),
                      Row(
                        children: [
                          Icon(order.paymentMethod == 'cod' ? Icons.handshake_outlined : Icons.account_balance_outlined, size: 10, color: order.paymentMethod == 'cod' ? Colors.green : Colors.blue),
                          const SizedBox(width: 4),
                          Text(order.paymentMethod?.toUpperCase() ?? '', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w900, color: order.paymentMethod == 'cod' ? Colors.green : Colors.blue)),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(formatter.format(order.totalAmount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14, color: const Color(0xFF9F1521))),
                  const SizedBox(height: 5),
                  ElevatedButton(
                    onPressed: () => _showOrderDetail(order),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF9F1521),
                      foregroundColor: Colors.white,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 0),
                      minimumSize: const Size(0, 28),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    ),
                    child: Text('Cek Detail', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold)),
                  ),
                ],
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
      case 'pending_payment':
        color = Colors.orange;
        bgColor = Colors.orange.shade50;
        label = 'BELUM BAYAR';
        break;
      case 'paid_verifying':
        color = Colors.blue;
        bgColor = Colors.blue.shade50;
        label = 'PERLU VERIFIKASI';
        break;
      case 'processing':
        color = Colors.cyan;
        bgColor = Colors.cyan.shade50;
        label = 'DIPROSES';
        break;
      case 'completed':
        color = Colors.green;
        bgColor = Colors.green.shade50;
        label = 'SELESAI';
        break;
      default:
        color = Colors.red;
        bgColor = Colors.red.shade50;
        label = status.toUpperCase();
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(6), border: Border.all(color: color.withOpacity(0.3))),
      child: Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  void _showOrderDetail(Order order) {
    final trackingController = TextEditingController(text: order.trackingNumber);
    
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(25))),
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom, left: 25, right: 25, top: 25),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 20),
              Text('Detail Pesanan', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
              const SizedBox(height: 20),
              
              // Info Card
              Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200, style: BorderStyle.solid)),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('ID PESANAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text('#TPD-${order.id}', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: const Color(0xFF1A1A1A))),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text('PEMBELI', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                        Text(order.user?.name ?? '-', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: const Color(0xFF1A1A1A))),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              
              Text('ALAMAT PENGIRIMAN / TITIK COD', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
              const SizedBox(height: 8),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                child: Row(
                  children: [
                    const Icon(Icons.location_on, color: Color(0xFF9F1521), size: 16),
                    const SizedBox(width: 10),
                    Expanded(child: Text(order.shippingAddress ?? 'Tidak ada alamat', style: GoogleFonts.plusJakartaSans(fontSize: 12))),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('METODE PEMBAYARAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                      const SizedBox(height: 4),
                      Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4), decoration: BoxDecoration(color: Colors.black, borderRadius: BorderRadius.circular(6)), child: Text(order.paymentMethod?.toUpperCase() ?? '', style: GoogleFonts.plusJakartaSans(fontSize: 8, color: Colors.white, fontWeight: FontWeight.w900))),
                    ],
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text('TOTAL PEMBAYARAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                      Text(NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(order.totalAmount), style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                    ],
                  ),
                ],
              ),
              
              if (order.paymentProof != null) ...[
                const SizedBox(height: 20),
                Text('BUKTI PEMBAYARAN', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey)),
                const SizedBox(height: 8),
                ClipRRect(
                  borderRadius: BorderRadius.circular(15),
                  child: Image.network(
                    order.paymentProof!,
                    width: double.infinity,
                    height: 200,
                    fit: BoxFit.cover,
                  ),
                ),
              ],
              
              const SizedBox(height: 30),
              
              // Actions
              if (order.status == 'paid_verifying') 
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () async {
                          final success = await _sellerService.approvePayment(order.id);
                          if (success && mounted) {
                            Navigator.pop(context);
                            _loadData();
                          }
                        },
                        style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), padding: const EdgeInsets.symmetric(vertical: 15)),
                        child: Text(order.paymentMethod == 'cod' ? 'Terima Pesanan' : 'Konfirmasi Bayar', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () async {
                          final success = await _sellerService.rejectPayment(order.id);
                          if (success && mounted) {
                            Navigator.pop(context);
                            _loadData();
                          }
                        },
                        style: OutlinedButton.styleFrom(foregroundColor: Colors.black, side: const BorderSide(color: Colors.black), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), padding: const EdgeInsets.symmetric(vertical: 15)),
                        child: Text(order.paymentMethod == 'cod' ? 'Tolak / Batalkan' : 'Tolak', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                      ),
                    ),
                  ],
                )
              else if (order.status == 'processing')
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
                  child: Column(
                    children: [
                      Text(order.paymentMethod == 'cod' ? 'Catatan Serah Terima' : 'Nomor Resi / Info Kurir', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                      const SizedBox(height: 10),
                      TextField(
                        controller: trackingController,
                        decoration: InputDecoration(hintText: order.paymentMethod == 'cod' ? 'Serah terima di...' : 'RESI12345...', filled: true, fillColor: Colors.white, border: OutlineInputBorder(borderRadius: BorderRadius.circular(25), borderSide: BorderSide.none), contentPadding: const EdgeInsets.symmetric(horizontal: 20)),
                      ),
                      const SizedBox(height: 15),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: () async {
                            final success = await _sellerService.updateTracking(order.id, trackingController.text);
                            if (success && mounted) {
                              Navigator.pop(context);
                              _loadData();
                            }
                          },
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.black, foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), padding: const EdgeInsets.symmetric(vertical: 12)),
                          child: Text(order.paymentMethod == 'cod' ? 'Selesaikan Pesanan' : 'Update Tracking', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Center(
                        child: TextButton(
                          onPressed: () async {
                            final success = await _sellerService.rejectPayment(order.id);
                            if (success && mounted) {
                              Navigator.pop(context);
                              _loadData();
                            }
                          },
                          child: Text('Batalkan Pesanan', style: GoogleFonts.plusJakartaSans(color: Colors.red, fontWeight: FontWeight.bold)),
                        ),
                      ),
                    ],
                  ),
                ),
              
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }
}
