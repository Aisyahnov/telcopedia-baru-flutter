import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import '../services/checkout_service.dart';
import '../models/order.dart';

class PaymentScreen extends StatefulWidget {
  final Order order;
  const PaymentScreen({super.key, required this.order});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  final CheckoutService _checkoutService = CheckoutService();
  final ImagePicker _picker = ImagePicker();
  XFile? _image;
  bool _isUploading = false;

  Future<void> _pickImage() async {
    final XFile? pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() => _image = pickedFile);
    }
  }

  Future<void> _handleUpload() async {
    if (_image == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Pilih bukti transfer terlebih dahulu')));
      return;
    }

    setState(() => _isUploading = true);

    final updatedOrder = await _checkoutService.uploadPaymentProof(widget.order.id, _image!);

    if (mounted) {
      setState(() => _isUploading = false);
      if (updatedOrder != null) {
        showDialog(
          context: context,
          builder: (c) => AlertDialog(
            title: const Text('Pembayaran Dikirim!'),
            content: const Text('Terima kasih! Bukti transfer Anda telah dikirim dan akan diverifikasi oleh seller.'),
            actions: [
              TextButton(
                onPressed: () => Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false),
                child: const Text('Selesai'),
              ),
            ],
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal mengunggah bukti pembayaran')));
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
        title: Text(
          'Pembayaran',
          style: GoogleFonts.plusJakartaSans(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            _buildInstructions(format),
            const SizedBox(height: 25),
            _buildUploadSection(),
            const SizedBox(height: 30),
            _buildSubmitButton(),
            const SizedBox(height: 20),
            TextButton(
              onPressed: () => Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false),
              child: Text('Bayar Nanti', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInstructions(NumberFormat format) {
    return Container(
      padding: const EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 15)],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(color: const Color(0xFF9F1521), borderRadius: BorderRadius.circular(100)),
            child: Text('Langkah 1: Transfer', style: GoogleFonts.plusJakartaSans(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
          ),
          const SizedBox(height: 15),
          Text('Metode Transfer Bank', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 18)),
          const SizedBox(height: 5),
          Text(
            'Silakan transfer ke salah satu rekening resmi Telcopedia berikut:',
            textAlign: TextAlign.center,
            style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12),
          ),
          const SizedBox(height: 25),
          _buildBankItem('Mandiri', '131-00-1234-5678', 'a/n Telcopedia Mahasiswa', 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg'),
          const SizedBox(height: 15),
          _buildBankItem('DANA', '0812-3456-7890', 'a/n Telcopedia Mahasiswa', 'https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg'),
          const SizedBox(height: 25),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(color: const Color(0xFFFFF5F5), borderRadius: BorderRadius.circular(15)),
            child: Column(
              children: [
                Text('Nominal Transfer:', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.black54)),
                Text(format.format(widget.order.totalAmount), style: GoogleFonts.plusJakartaSans(fontSize: 24, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBankItem(String name, String number, String holder, String logoUrl) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: const Color(0xFFF8F9FA), borderRadius: BorderRadius.circular(15), border: Border.all(color: Colors.grey.shade200)),
      child: Row(
        children: [
          Image.network(logoUrl, width: 40, height: 25, fit: BoxFit.contain, errorBuilder: (c, e, s) => const Icon(Icons.account_balance)),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(name, style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
                Text(number, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 15)),
                Text(holder, style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 10)),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.copy, size: 18, color: Colors.grey),
            onPressed: () {
              // Copy to clipboard logic
            },
          ),
        ],
      ),
    );
  }

  Widget _buildUploadSection() {
    return Container(
      padding: const EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 15)],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(color: const Color(0xFF9F1521), borderRadius: BorderRadius.circular(100)),
            child: Text('Langkah 2: Konfirmasi', style: GoogleFonts.plusJakartaSans(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
          ),
          const SizedBox(height: 15),
          Text('Upload Bukti Transfer', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 18)),
          const SizedBox(height: 20),
          GestureDetector(
            onTap: _pickImage,
            child: Container(
              width: double.infinity,
              height: 200,
              decoration: BoxDecoration(
                color: const Color(0xFFF8F9FA),
                borderRadius: BorderRadius.circular(15),
                border: Border.all(color: _image != null ? Colors.green : Colors.grey.shade300, style: BorderStyle.solid),
              ),
              child: _image != null
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(15),
                      child: kIsWeb 
                        ? Image.network(_image!.path, fit: BoxFit.cover)
                        : Image.network(_image!.path, fit: BoxFit.cover), // On web, pickedFile.path is a blob URL
                    )
                  : Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.cloud_upload_outlined, size: 50, color: Colors.grey),
                        const SizedBox(height: 10),
                        Text('Ketuk untuk pilih foto struk', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12, fontWeight: FontWeight.bold)),
                      ],
                    ),
            ),
          ),
          if (_image != null)
            Padding(
              padding: const EdgeInsets.only(top: 10),
              child: Text('Bukti transfer terpilih!', style: GoogleFonts.plusJakartaSans(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold)),
            ),
        ],
      ),
    );
  }

  Widget _buildSubmitButton() {
    return ElevatedButton(
      onPressed: _isUploading ? null : _handleUpload,
      style: ElevatedButton.styleFrom(
        backgroundColor: const Color(0xFF9F1521),
        foregroundColor: Colors.white,
        minimumSize: const Size(double.infinity, 55),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      ),
      child: _isUploading 
        ? const CircularProgressIndicator(color: Colors.white)
        : Text('Kirim Konfirmasi', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16)),
    );
  }
}
