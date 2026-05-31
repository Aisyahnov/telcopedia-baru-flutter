import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../services/cart_service.dart';
import '../models/cart.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final CartService _cartService = CartService();
  Cart? _cart;
  Map<String, dynamic>? _totals;
  bool _isLoading = true;
  final List<int> _selectedItemIds = [];
  final TextEditingController _voucherController = TextEditingController();
  String? _appliedVoucher;
  double _voucherDiscount = 0;

  @override
  void initState() {
    super.initState();
    _loadCart();
  }

  Future<void> _loadCart() async {
    final result = await _cartService.getCart();
    if (mounted) {
      setState(() {
        _cart = result?['cart'];
        _totals = result?['total'];
        _isLoading = false;
      });
    }
  }

  void _toggleSelection(int id) {
    setState(() {
      if (_selectedItemIds.contains(id)) {
        _selectedItemIds.remove(id);
      } else {
        _selectedItemIds.add(id);
      }
    });
  }

  void _toggleAll(bool? selected) {
    if (selected == null || _cart == null) return;
    setState(() {
      _selectedItemIds.clear();
      if (selected) {
        _selectedItemIds.addAll(_cart!.items.map((i) => i.id));
      }
    });
  }

  Future<void> _updateQty(int itemId, int newQty) async {
    if (newQty < 1) return;
    final success = await _cartService.updateQuantity(itemId, newQty);
    if (success) _loadCart();
  }

  Future<void> _removeItem(int itemId) async {
    final success = await _cartService.removeItem(itemId);
    if (success) {
      _selectedItemIds.remove(itemId);
      _loadCart();
    }
  }

  Future<void> _applyVoucher() async {
    if (_voucherController.text.isEmpty) return;
    
    // For now, local validation or simple service call
    final result = await _cartService.applyVoucher(_voucherController.text);
    if (mounted) {
      if (result != null && result['success'] == true) {
        setState(() {
          _appliedVoucher = _voucherController.text;
          _voucherDiscount = (result['discount'] as num).toDouble();
        });
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Voucher berhasil digunakan!')));
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result?['message'] ?? 'Voucher tidak valid')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormat = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        foregroundColor: const Color(0xFF9F1521),
        title: Text(
          'Keranjang Belanja',
          style: GoogleFonts.plusJakartaSans(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : (_cart == null || _cart!.items.isEmpty)
              ? _buildEmptyState()
              : Column(
                  children: [
                    _buildHeader(),
                    Expanded(
                      child: ListView.builder(
                        itemCount: _cart!.items.length,
                        itemBuilder: (context, index) {
                          final item = _cart!.items[index];
                          return _buildCartItem(item, currencyFormat);
                        },
                      ),
                    ),
                    _buildSummary(currencyFormat),
                  ],
                ),
    );
  }

  Widget _buildHeader() {
    final allSelected = _cart != null && _selectedItemIds.length == _cart!.items.length && _cart!.items.isNotEmpty;
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
      child: Row(
        children: [
          Checkbox(
            value: allSelected,
            onChanged: _toggleAll,
            activeColor: const Color(0xFF9F1521),
          ),
          Text(
            'Pilih Semua',
            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14),
          ),
          const Spacer(),
          Text(
            '${_selectedItemIds.length} Produk terpilih',
            style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12),
          ),
        ],
      ),
    );
  }

  Widget _buildCartItem(CartItem item, NumberFormat format) {
    final isSelected = _selectedItemIds.contains(item.id);
    return Container(
      margin: const EdgeInsets.only(top: 10, left: 15, right: 15),
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Checkbox(
            value: isSelected,
            onChanged: (_) => _toggleSelection(item.id),
            activeColor: const Color(0xFF9F1521),
          ),
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: Image.network(
              item.product?.imageUrl ?? '',
              width: 70,
              height: 70,
              fit: BoxFit.cover,
              errorBuilder: (c, e, s) => Container(color: Colors.grey.shade100, width: 70, height: 70),
            ),
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.product?.name ?? 'Produk',
                  style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 14),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  format.format(item.product?.price ?? 0),
                  style: GoogleFonts.plusJakartaSans(color: const Color(0xFF9F1521), fontWeight: FontWeight.w800, fontSize: 14),
                ),
                const SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        _buildQtyBtn(Icons.remove, () => _updateQty(item.id, item.quantity - 1)),
                        GestureDetector(
                          onTap: () => _showEditQtyDialog(item),
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 12),
                            child: Text('${item.quantity}', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                          ),
                        ),
                        _buildQtyBtn(Icons.add, item.quantity < (item.product?.stock ?? 0) ? () => _updateQty(item.id, item.quantity + 1) : null),
                      ],
                    ),
                    IconButton(
                      icon: const Icon(Icons.delete_outline, color: Colors.grey, size: 20),
                      onPressed: () => _removeItem(item.id),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _showEditQtyDialog(CartItem item) {
    final controller = TextEditingController(text: item.quantity.toString());
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Ubah Jumlah', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Maksimal stok: ${item.product?.stock ?? 0}', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey)),
            const SizedBox(height: 10),
            TextField(
              controller: controller,
              keyboardType: TextInputType.number,
              autofocus: true,
              decoration: InputDecoration(
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                contentPadding: const EdgeInsets.symmetric(horizontal: 15),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () {
              final newQty = int.tryParse(controller.text) ?? item.quantity;
              if (newQty > (item.product?.stock ?? 0)) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Stok tidak mencukupi (Maks: ${item.product?.stock})')));
                return;
              }
              _updateQty(item.id, newQty);
              Navigator.pop(context);
            },
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  Widget _buildQtyBtn(IconData icon, VoidCallback? onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          color: onTap == null ? Colors.grey.shade50 : Colors.grey.shade100,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.grey.shade200),
        ),
        child: Icon(icon, size: 16, color: onTap == null ? Colors.grey.shade300 : Colors.black87),
      ),
    );
  }

  Widget _buildSummary(NumberFormat format) {
    // Calculate subtotal for selected items locally for instant feedback
    double selectedSubtotal = 0;
    if (_cart != null) {
      for (var item in _cart!.items) {
        if (_selectedItemIds.contains(item.id)) {
          selectedSubtotal += (item.quantity * (item.product?.price ?? 0));
        }
      }
    }
    double adminFee = selectedSubtotal * 0.05;
    double total = selectedSubtotal + adminFee - _voucherDiscount;
    if (total < 0) total = 0;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 10, offset: const Offset(0, -5))],
        borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          _buildSummaryRow('Subtotal', format.format(selectedSubtotal)),
          _buildSummaryRow('Biaya Layanan (5%)', format.format(adminFee)),
          if (_voucherDiscount > 0)
            _buildSummaryRow('Diskon Voucher', '- ${format.format(_voucherDiscount)}', isSuccess: true),
          _buildSummaryRow('Pengiriman', 'GRATIS COD', isSuccess: true),
          const SizedBox(height: 15),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _voucherController,
                  decoration: InputDecoration(
                    hintText: 'Kode Voucher',
                    hintStyle: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey),
                    filled: true,
                    fillColor: Colors.grey.shade50,
                    contentPadding: const EdgeInsets.symmetric(horizontal: 15, vertical: 0),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade200)),
                    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(10), borderSide: BorderSide(color: Colors.grey.shade200)),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              ElevatedButton(
                onPressed: _applyVoucher,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.black87,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  elevation: 0,
                ),
                child: const Text('Pakai'),
              ),
            ],
          ),
          const Divider(height: 30),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Total Bayar', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 16)),
              Text(format.format(total), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 20, color: const Color(0xFF9F1521))),
            ],
          ),
          const SizedBox(height: 20),
          ElevatedButton(
            onPressed: _selectedItemIds.isEmpty ? null : () {
              final selectedItems = _cart!.items.where((i) => _selectedItemIds.contains(i.id)).toList();
              Navigator.pushNamed(context, '/checkout', arguments: {
                'items': selectedItems,
                'cartItemIds': _selectedItemIds.join(','),
                'appliedVoucher': _appliedVoucher,
                'voucherDiscount': _voucherDiscount,
              });
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF9F1521),
              foregroundColor: Colors.white,
              minimumSize: const Size(double.infinity, 55),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              elevation: 0,
            ),
            child: Text('Lanjut ke Pembayaran', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16)),
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

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.shopping_cart_outlined, size: 100, color: Colors.grey.shade200),
          const SizedBox(height: 20),
          Text('Keranjangmu Kosong', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 18)),
          Text('Ayo cari produk menarik sekarang!', style: GoogleFonts.plusJakartaSans(color: Colors.grey)),
          const SizedBox(height: 30),
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
            child: const Text('Mulai Belanja'),
          ),
        ],
      ),
    );
  }
}
