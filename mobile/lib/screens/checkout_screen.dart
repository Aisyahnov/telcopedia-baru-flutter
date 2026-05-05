import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../services/checkout_service.dart';
import '../models/cart.dart';
import '../models/order.dart' as model;
import '../providers/auth_provider.dart';
import '../services/chat_service.dart';
import '../models/chat.dart';

class CheckoutScreen extends StatefulWidget {
  final List<CartItem> items;
  final String? cartItemIds;
  final int? productId;

  const CheckoutScreen({
    super.key, 
    required this.items, 
    this.cartItemIds, 
    this.productId,
    this.appliedVoucher,
    this.voucherDiscount = 0,
  });

  final String? appliedVoucher;
  final double voucherDiscount;

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}



class _CheckoutScreenState extends State<CheckoutScreen> {
  final CheckoutService _checkoutService = CheckoutService();
  final ChatService _chatService = ChatService();
  final TextEditingController _addressController = TextEditingController();
  String _paymentMethod = 'transfer';
  bool _isEditingAddress = false;
  bool _isProcessing = false;

  @override
  void initState() {
    super.initState();
    final user = Provider.of<AuthProvider>(context, listen: false).user;
    _addressController.text = user?.address ?? '';
  }

  double get _subtotal => widget.items.fold(0, (sum, item) => sum + (item.quantity * (item.product?.price ?? 0)));
  double get _adminFee => _subtotal * 0.05;
  double get _total => (_subtotal + _adminFee - widget.voucherDiscount).clamp(0, double.infinity);

  Future<void> _handlePlaceOrder() async {
    if (_addressController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Alamat pengiriman harus diisi')));
      return;
    }

    setState(() => _isProcessing = true);

    final order = await _checkoutService.processCheckout(
      shippingAddress: _addressController.text,
      paymentMethod: _paymentMethod,
      productId: widget.productId,
      cartItemIds: widget.cartItemIds,
      voucherCode: widget.appliedVoucher,
    );

    if (mounted) {
      setState(() => _isProcessing = false);
      if (order != null) {
        if (_paymentMethod == 'transfer') {
          Navigator.pushReplacementNamed(context, '/payment', arguments: order);
        } else {
          // COD success
        // COD success
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (c) => AlertDialog(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
            title: Column(
              children: [
                const Icon(Icons.check_circle, color: Colors.green, size: 60),
                const SizedBox(height: 15),
                Text('Pesanan Berhasil!', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
              ],
            ),
            content: Text(
              'Pesanan COD Anda telah dibuat. Silakan hubungi seller untuk menentukan lokasi dan waktu janji temu.',
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(fontSize: 14),
            ),
            actions: [
              Column(
                children: [
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () async {
                        final product = order.items.first.product;
                        if (product != null) {
                          final chat = await _chatService.getOrCreateChat(product.sellerId, product.id);
                          if (chat != null && mounted) {
                            Navigator.pushNamed(context, '/chat/room', arguments: chat);
                          }
                        }
                      },
                      icon: const Icon(Icons.chat_bubble_outline, size: 18),
                      label: const Text('Chat Seller Sekarang'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF9F1521),
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  SizedBox(
                    width: double.infinity,
                    child: TextButton(
                      onPressed: () => Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false),
                      child: Text('Kembali ke Beranda', style: TextStyle(color: Colors.grey.shade600)),
                    ),
                  ),
                ],
              ),
            ],
          ),
        );
      }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal membuat pesanan')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final format = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        foregroundColor: const Color(0xFF9F1521),
        title: Text(
          'Checkout',
          style: GoogleFonts.plusJakartaSans(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionHeader('Alamat Pengiriman', Icons.location_on_outlined),
            _buildAddressCard(),
            const SizedBox(height: 25),
            _buildSectionHeader('Rincian Produk', Icons.shopping_bag_outlined),
            _buildProductList(format),
            const SizedBox(height: 25),
            _buildSectionHeader('Metode Pembayaran', Icons.credit_card_outlined),
            _buildPaymentMethods(),
            const SizedBox(height: 25),
            _buildSummaryCard(format),
            const SizedBox(height: 30),
          ],
        ),
      ),
      bottomNavigationBar: _buildBottomAction(format),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Icon(icon, size: 18, color: const Color(0xFF9F1521)),
          const SizedBox(width: 8),
          Text(title, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 14)),
        ],
      ),
    );
  }

  Widget _buildAddressCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: const Border(left: BorderSide(color: Color(0xFF9F1521), width: 4)),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                Provider.of<AuthProvider>(context).user?.name ?? 'User',
                style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
              ),
              GestureDetector(
                onTap: () => setState(() => _isEditingAddress = !_isEditingAddress),
                child: Text(
                  _isEditingAddress ? 'Batal' : 'Ubah',
                  style: GoogleFonts.plusJakartaSans(color: const Color(0xFF9F1521), fontWeight: FontWeight.bold, fontSize: 12),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          _isEditingAddress
              ? Column(
                  children: [
                    TextField(
                      controller: _addressController,
                      maxLines: 3,
                      style: GoogleFonts.plusJakartaSans(fontSize: 13),
                      decoration: InputDecoration(
                        hintText: 'Masukkan Titik Temu / Alamat Detail...',
                        filled: true,
                        fillColor: Colors.grey.shade50,
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                      ),
                    ),
                    const SizedBox(height: 10),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: () => setState(() => _isEditingAddress = false),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF9F1521),
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                        child: const Text('Simpan Alamat', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                      ),
                    ),
                  ],
                )
              : Text(
                  _addressController.text.isEmpty ? 'Alamat belum diatur.' : _addressController.text,
                  style: GoogleFonts.plusJakartaSans(color: Colors.grey.shade600, fontSize: 13),
                ),
        ],
      ),
    );
  }

  Widget _buildProductList(NumberFormat format) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: widget.items.length,
        separatorBuilder: (context, index) => Divider(height: 1, color: Colors.grey.shade100),
        itemBuilder: (context, index) {
          final item = widget.items[index];
          return Padding(
            padding: const EdgeInsets.all(15),
            child: Row(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: Image.network(item.product?.imageUrl ?? '', width: 50, height: 50, fit: BoxFit.cover),
                ),
                const SizedBox(width: 15),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(item.product?.name ?? 'Produk', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13), maxLines: 1, overflow: TextOverflow.ellipsis),
                      Text('${item.quantity} x ${format.format(item.product?.price ?? 0)}', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 11)),
                    ],
                  ),
                ),
                Text(format.format((item.product?.price ?? 0) * item.quantity), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildPaymentMethods() {
    return Column(
      children: [
        _buildPaymentOption('transfer', 'Transfer Bank', 'Verifikasi manual oleh seller', Icons.account_balance_outlined),
        const SizedBox(height: 10),
        _buildPaymentOption('cod', 'COD (Bayar di Tempat)', 'Ketemuan langsung di area kampus', Icons.handshake_outlined),
      ],
    );
  }

  Widget _buildPaymentOption(String value, String title, String subtitle, IconData icon) {
    final isSelected = _paymentMethod == value;
    return GestureDetector(
      onTap: () => setState(() => _paymentMethod = value),
      child: Container(
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFFFFF9F9) : Colors.white,
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: isSelected ? const Color(0xFF9F1521) : Colors.grey.shade200),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: const Color(0xFF9F1521), size: 20),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
                  Text(subtitle, style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 10)),
                ],
              ),
            ),
            Radio<String>(
              value: value,
              groupValue: _paymentMethod,
              onChanged: (v) => setState(() => _paymentMethod = v!),
              activeColor: const Color(0xFF9F1521),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryCard(NumberFormat format) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 15)],
      ),
      child: Column(
        children: [
          _buildSummaryRow('Total Harga (${widget.items.length} barang)', format.format(_subtotal)),
          _buildSummaryRow('Biaya Layanan (5%)', format.format(_adminFee)),
          if (widget.voucherDiscount > 0)
            _buildSummaryRow('Diskon Voucher', '- ${format.format(widget.voucherDiscount)}', isSuccess: true),
          _buildSummaryRow('Biaya Pengiriman', 'GRATIS COD', isSuccess: true),
          const Divider(height: 30),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Total Tagihan', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 16)),
              Text(format.format(_total), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 18, color: const Color(0xFF9F1521))),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool isSuccess = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 13)),
          Text(value, style: GoogleFonts.plusJakartaSans(color: isSuccess ? Colors.green : Colors.black87, fontWeight: FontWeight.bold, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildBottomAction(NumberFormat format) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))],
      ),
      child: ElevatedButton(
        onPressed: _isProcessing ? null : _handlePlaceOrder,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFF9F1521),
          foregroundColor: Colors.white,
          minimumSize: const Size(double.infinity, 55),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
        ),
        child: _isProcessing 
          ? const CircularProgressIndicator(color: Colors.white)
          : Text('Konfirmasi & Buat Pesanan', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16)),
      ),
    );
  }
}
