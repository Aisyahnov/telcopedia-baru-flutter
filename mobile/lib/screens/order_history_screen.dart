import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import '../services/order_service.dart';
import '../models/order.dart';

class OrderHistoryScreen extends StatefulWidget {
  const OrderHistoryScreen({super.key});

  @override
  State<OrderHistoryScreen> createState() => _OrderHistoryScreenState();
}

class _OrderHistoryScreenState extends State<OrderHistoryScreen> {
  final OrderService _orderService = OrderService();
  List<Order> _orders = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  Future<void> _loadOrders() async {
    final orders = await _orderService.getBuyerOrders();
    if (mounted) {
      setState(() {
        _orders = orders;
        _isLoading = false;
      });
    }
  }

  Future<void> _handleComplete(int orderId) async {
    final success = await _orderService.completeOrder(orderId);
    if (success) _loadOrders();
  }

  Future<void> _handleCancel(int orderId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Batalkan Pesanan?', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Text('Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.', style: GoogleFonts.plusJakartaSans()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Kembali')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      setState(() => _isLoading = true);
      final success = await _orderService.cancelOrder(orderId);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success ? 'Pesanan dibatalkan & stok dikembalikan' : 'Gagal membatalkan pesanan')));
        _loadOrders();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Text(
          'Riwayat Pesanan',
          style: GoogleFonts.plusJakartaSans(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : RefreshIndicator(
              onRefresh: _loadOrders,
              color: const Color(0xFF9F1521),
              child: _orders.isEmpty ? _buildEmptyState() : _buildOrderList(),
            ),
    );
  }

  Widget _buildEmptyState() {
    return ListView(
      children: [
        SizedBox(height: MediaQuery.of(context).size.height * 0.2),
        const Icon(Icons.shopping_bag_outlined, size: 80, color: Colors.grey),
        const SizedBox(height: 20),
        Center(child: Text('Belum ada pesanan.', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16))),
        const SizedBox(height: 10),
        Center(child: Text('Ayo mulai belanja barang menarik di Telcopedia!', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 13))),
      ],
    );
  }

  Widget _buildOrderList() {
    return ListView.builder(
      padding: const EdgeInsets.all(15),
      itemCount: _orders.length,
      itemBuilder: (context, index) => _buildOrderCard(_orders[index]),
    );
  }

  Widget _buildOrderCard(Order order) {
    final format = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
      ),
      child: Column(
        children: [
          _buildOrderHeader(order),
          _buildOrderItems(order, format),
          _buildOrderFooter(order, format),
        ],
      ),
    );
  }

  Widget _buildOrderHeader(Order order) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 12),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: const BorderRadius.vertical(top: Radius.circular(20))),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Pesanan #ORD-${order.id}', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 13)),
              Text(DateFormat('dd MMM yyyy').format(order.createdAt), style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 11)),
            ],
          ),
          _buildStatusBadge(order.status),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bgColor;
    Color textColor;
    String label;

    switch (status) {
      case 'pending_payment':
        bgColor = Colors.orange.shade50;
        textColor = Colors.orange.shade800;
        label = 'BELUM BAYAR';
        break;
      case 'paid_verifying':
        bgColor = Colors.blue.shade50;
        textColor = Colors.blue.shade800;
        label = 'VERIFIKASI';
        break;
      case 'processing':
        bgColor = Colors.purple.shade50;
        textColor = Colors.purple.shade800;
        label = 'DIPROSES';
        break;
      case 'shipped':
        bgColor = Colors.indigo.shade50;
        textColor = Colors.indigo.shade800;
        label = 'DIKIRIM';
        break;
      case 'completed':
        bgColor = Colors.green.shade50;
        textColor = Colors.green.shade800;
        label = 'SELESAI';
        break;
      default:
        bgColor = Colors.grey.shade100;
        textColor = Colors.grey.shade600;
        label = status.toUpperCase();
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(6)),
      child: Text(label, style: GoogleFonts.plusJakartaSans(color: textColor, fontWeight: FontWeight.bold, fontSize: 9)),
    );
  }

  Widget _buildOrderItems(Order order, NumberFormat format) {
    return ListView.separated(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: order.items.length,
      separatorBuilder: (c, i) => Divider(height: 1, color: Colors.grey.shade100),
      itemBuilder: (context, index) {
        final item = order.items[index];
        return Padding(
          padding: const EdgeInsets.all(15),
          child: Column(
            children: [
              Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(8),
                    child: Image.network(item.product?.imageUrl ?? '', width: 50, height: 50, fit: BoxFit.cover),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(item.product?.name ?? 'Produk', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13)),
                        Text('${item.quantity} x ${format.format(item.price)}', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 11)),
                      ],
                    ),
                  ),
                ],
              ),
              if (order.status == 'completed' || order.status == 'shipped')
                Padding(
                  padding: const EdgeInsets.only(top: 12),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      _buildActionBtn('Retur', Colors.grey.shade100, Colors.black87, () => _showReturnDialog(order, item)),
                      if (order.status == 'completed') ...[
                        const SizedBox(width: 10),
                        _buildActionBtn('Ulasan', const Color(0xFF9F1521), Colors.white, () => _showReviewDialog(order, item)),
                      ]
                    ],
                  ),
                ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildActionBtn(String label, Color bg, Color text, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 6),
        decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(100), border: bg == Colors.white ? Border.all(color: Colors.grey.shade200) : null),
        child: Text(label, style: GoogleFonts.plusJakartaSans(color: text, fontWeight: FontWeight.bold, fontSize: 11)),
      ),
    );
  }

  Widget _buildOrderFooter(Order order, NumberFormat format) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(border: Border(top: BorderSide(color: Colors.grey.shade100))),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (order.shippingAddress.isNotEmpty)
            Container(
              margin: const EdgeInsets.only(bottom: 15),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(12),
                border: const Border(left: BorderSide(color: Color(0xFF9F1521), width: 4)),
              ),
              child: Row(
                children: [
                  const Icon(Icons.location_on, size: 16, color: Color(0xFF9F1521)),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      order.shippingAddress,
                      style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade700),
                    ),
                  ),
                ],
              ),
            ),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Total Pesanan', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12)),
              Text(format.format(order.totalAmount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, color: const Color(0xFF9F1521), fontSize: 16)),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(6), border: Border.all(color: Colors.grey.shade200)),
                child: Text(order.paymentMethod.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold)),
              ),
              const Spacer(),
              if (order.trackingNumber != null)
                Text('Resi: ${order.trackingNumber}', style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.blue, fontWeight: FontWeight.bold)),
            ],
          ),
          if (order.status == 'pending_payment' && order.paymentMethod != 'cod')
            Padding(
              padding: const EdgeInsets.only(top: 15),
              child: order.paymentProof != null
                  ? Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(10)),
                      child: Row(
                        children: [
                          const Icon(Icons.check_circle, color: Colors.green, size: 16),
                          const SizedBox(width: 8),
                          Text('Bukti Terkirim. Tunggu verifikasi.', style: GoogleFonts.plusJakartaSans(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    )
                  : ElevatedButton.icon(
                      onPressed: () => _pickAndUploadProof(order.id),
                      icon: const Icon(Icons.upload_file, size: 18),
                      label: const Text('Upload Bukti Transfer'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.white,
                        foregroundColor: const Color(0xFF9F1521),
                        side: const BorderSide(color: Color(0xFF9F1521)),
                        minimumSize: const Size(double.infinity, 45),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        elevation: 0,
                      ),
                    ),
            ),
          if (order.status == 'shipped' || order.status == 'processing')
            Padding(
              padding: const EdgeInsets.only(top: 15),
              child: ElevatedButton(
                onPressed: () => _handleComplete(order.id),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green,
                  foregroundColor: Colors.white,
                  minimumSize: const Size(double.infinity, 45),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                ),
                child: Text('Pesanan Diterima', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
              ),
            ),
          if (order.status == 'pending_payment' || order.status == 'paid_verifying' || order.status == 'processing' || order.status == 'shipped')
            Padding(
              padding: const EdgeInsets.only(top: 10),
              child: TextButton(
                onPressed: () => _handleCancel(order.id),
                child: Text('Batalkan Pesanan', style: GoogleFonts.plusJakartaSans(color: const Color(0xFF9F1521), fontWeight: FontWeight.bold, fontSize: 12)),
              ),
            ),
        ],
      ),
    );
  }

  Future<void> _pickAndUploadProof(int orderId) async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery);
    if (image == null) return;

    setState(() => _isLoading = true);
    final success = await _orderService.uploadPaymentProof(orderId, image);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success ? 'Bukti berhasil diupload!' : 'Gagal upload bukti')));
      _loadOrders();
    }
  }

  void _showReviewDialog(Order order, OrderItem item) {
    showDialog(
      context: context,
      builder: (context) => ReviewDialog(orderId: order.id, productId: item.productId, productName: item.product?.name ?? 'Produk'),
    );
  }

  void _showReturnDialog(Order order, OrderItem item) {
    showDialog(
      context: context,
      builder: (context) => ReturnDialog(orderId: order.id, productId: item.productId, productName: item.product?.name ?? 'Produk'),
    );
  }
}

class ReviewDialog extends StatefulWidget {
  final int orderId;
  final int productId;
  final String productName;
  const ReviewDialog({super.key, required this.orderId, required this.productId, required this.productName});

  @override
  State<ReviewDialog> createState() => _ReviewDialogState();
}

class _ReviewDialogState extends State<ReviewDialog> {
  int _rating = 5;
  int _sellerRating = 5;
  final TextEditingController _commentController = TextEditingController();
  final TextEditingController _sellerCommentController = TextEditingController();
  final OrderService _orderService = OrderService();
  bool _isSubmitting = false;
  XFile? _media;

  Future<void> _pickMedia() async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() => _media = image);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: Text('Beri Ulasan', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('ULASAN PRODUK', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey)),
            const SizedBox(height: 5),
            Text(widget.productName, style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(5, (index) {
                return IconButton(
                  icon: Icon(index < _rating ? Icons.star : Icons.star_border, color: Colors.amber, size: 30),
                  onPressed: () => setState(() => _rating = index + 1),
                );
              }),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _commentController,
              maxLines: 2,
              style: const TextStyle(fontSize: 13),
              decoration: InputDecoration(
                hintText: 'Tulis ulasan produk...',
                filled: true,
                fillColor: Colors.grey.shade50,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
            const SizedBox(height: 25),
            Text('ULASAN SELLER', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey)),
            const SizedBox(height: 15),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(5, (index) {
                return IconButton(
                  icon: Icon(index < _sellerRating ? Icons.star : Icons.star_border, color: Colors.blue, size: 30),
                  onPressed: () => setState(() => _sellerRating = index + 1),
                );
              }),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _sellerCommentController,
              maxLines: 2,
              style: const TextStyle(fontSize: 13),
              decoration: InputDecoration(
                hintText: 'Tulis ulasan seller...',
                filled: true,
                fillColor: Colors.grey.shade50,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
            const SizedBox(height: 20),
            GestureDetector(
              onTap: _pickMedia,
              child: Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
                child: Row(
                  children: [
                    Icon(Icons.camera_alt, color: Colors.grey.shade600, size: 20),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(_media == null ? 'Tambah Foto Produk (Opsional)' : 'Foto terpilih: ${_media!.name}', 
                        style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700)),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
        ElevatedButton(
          onPressed: _isSubmitting ? null : _submit,
          style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
          child: _isSubmitting ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)) : const Text('Kirim'),
        ),
      ],
    );
  }

  Future<void> _submit() async {
    setState(() => _isSubmitting = true);
    final success = await _orderService.storeReview(
      orderId: widget.orderId,
      productId: widget.productId,
      rating: _rating,
      comment: _commentController.text,
      sellerRating: _sellerRating,
      sellerComment: _sellerCommentController.text,
      media: _media,
    );
    if (mounted) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success ? 'Ulasan berhasil dikirim!' : 'Gagal mengirim ulasan')));
    }
  }
}

class ReturnDialog extends StatefulWidget {
  final int orderId;
  final int productId;
  final String productName;
  const ReturnDialog({super.key, required this.orderId, required this.productId, required this.productName});

  @override
  State<ReturnDialog> createState() => _ReturnDialogState();
}

class _ReturnDialogState extends State<ReturnDialog> {
  final TextEditingController _reasonController = TextEditingController();
  final OrderService _orderService = OrderService();
  bool _isSubmitting = false;
  XFile? _media;

  Future<void> _pickMedia() async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() => _media = image);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: Text('Ajukan Retur', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(widget.productName, style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey)),
            const SizedBox(height: 20),
            TextField(
              controller: _reasonController,
              maxLines: 4,
              decoration: InputDecoration(
                hintText: 'Alasan pengembalian...',
                filled: true,
                fillColor: Colors.grey.shade50,
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
            const SizedBox(height: 15),
            GestureDetector(
              onTap: _pickMedia,
              child: Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
                child: Row(
                  children: [
                    Icon(Icons.camera_alt, color: Colors.grey.shade600, size: 20),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(_media == null ? 'Tambah Bukti Foto (Opsional)' : 'Foto terpilih: ${_media!.name}', 
                        style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700)),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
        ElevatedButton(
          onPressed: _isSubmitting ? null : _submit,
          style: ElevatedButton.styleFrom(backgroundColor: Colors.black87, foregroundColor: Colors.white),
          child: _isSubmitting ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white)) : const Text('Ajukan'),
        ),
      ],
    );
  }

  Future<void> _submit() async {
    if (_reasonController.text.isEmpty) return;
    setState(() => _isSubmitting = true);
    final success = await _orderService.storeReturn(
      orderId: widget.orderId,
      productId: widget.productId,
      reason: _reasonController.text,
      media: _media,
    );
    if (mounted) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(success ? 'Pengajuan retur berhasil dikirim!' : 'Gagal mengirim pengajuan')));
    }
  }
}
