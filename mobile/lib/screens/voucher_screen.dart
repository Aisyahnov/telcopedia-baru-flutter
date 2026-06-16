import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

class VoucherScreen extends StatefulWidget {
  const VoucherScreen({super.key});

  @override
  State<VoucherScreen> createState() => _VoucherScreenState();
}

class _VoucherScreenState extends State<VoucherScreen> {
  final ApiService _apiService = ApiService();
  final ScrollController _scrollController = ScrollController();
  
  List<dynamic> _vouchers = [];
  bool _isLoading = true;
  bool _isLoadingMore = false;
  int _currentPage = 1;
  bool _hasMore = true;

  @override
  void initState() {
    super.initState();
    _loadVouchers();
    _scrollController.addListener(_scrollListener);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollListener() {
    if (_scrollController.position.pixels == _scrollController.position.maxScrollExtent) {
      if (_hasMore && !_isLoadingMore) {
        _loadMoreVouchers();
      }
    }
  }

  Future<void> _loadVouchers() async {
    setState(() { _isLoading = true; _currentPage = 1; _hasMore = true; _vouchers.clear(); });
    try {
      final response = await _apiService.dio.get('vouchers?page=$_currentPage');
      if (mounted) {
        setState(() {
          final List data = response.data['data'] ?? [];
          _vouchers = data;
          if (data.length < 10) _hasMore = false;
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _loadMoreVouchers() async {
    setState(() => _isLoadingMore = true);
    _currentPage++;
    try {
      final response = await _apiService.dio.get('vouchers?page=$_currentPage');
      if (mounted) {
        setState(() {
          final List data = response.data['data'] ?? [];
          _vouchers.addAll(data);
          if (data.length < 10) _hasMore = false;
          _isLoadingMore = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoadingMore = false);
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
          'Voucher Tersedia',
          style: GoogleFonts.plusJakartaSans(color: Colors.black, fontWeight: FontWeight.w900, fontSize: 18),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : _vouchers.isEmpty
              ? _buildEmptyState()
              : RefreshIndicator(
                  onRefresh: _loadVouchers,
                  color: const Color(0xFF9F1521),
                  child: ListView.builder(
                    controller: _scrollController,
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(20),
                    itemCount: _vouchers.length + (_hasMore ? 1 : 0),
                    itemBuilder: (context, index) {
                      if (index == _vouchers.length) {
                        return const Center(child: Padding(padding: EdgeInsets.all(15.0), child: CircularProgressIndicator(color: Color(0xFF9F1521))));
                      }
                      return _buildVoucherCard(_vouchers[index], format);
                    },
                  ),
                ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.confirmation_number_outlined, size: 80, color: Colors.grey.shade300),
          const SizedBox(height: 15),
          Text('Belum ada voucher', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildVoucherCard(Map<String, dynamic> voucher, NumberFormat format) {
    final minSpend = double.tryParse(voucher['min_spend']?.toString() ?? '0') ?? 0;
    
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      height: 130,
      child: Stack(
        children: [
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 10)],
              border: Border.all(color: Colors.grey.shade100),
            ),
            child: Row(
              children: [
                Container(
                  width: 100,
                  decoration: const BoxDecoration(
                    color: Color(0xFF9F1521),
                    borderRadius: BorderRadius.only(topLeft: Radius.circular(20), bottomLeft: Radius.circular(20)),
                  ),
                  child: const Center(child: Icon(Icons.confirmation_number, color: Colors.white, size: 40)),
                ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.all(15),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          voucher['code'],
                          style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 16, color: const Color(0xFF9F1521)),
                        ),
                        const SizedBox(height: 5),
                        Text(
                          'Potongan ${format.format(double.parse(voucher['discount_amount'].toString()))}',
                          style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700, fontWeight: FontWeight.w600),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 5),
                        Text(
                          minSpend > 0 ? 'Min. Belanja: ${format.format(minSpend)}' : 'Tanpa Min. Belanja',
                          style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.black87, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          voucher['valid_until'] != null ? 'Berlaku hingga ${DateFormat('dd MMM yyyy').format(DateTime.parse(voucher['valid_until']))}' : 'Tanpa Batas Waktu',
                          style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
          Positioned(
            right: 15,
            bottom: 15,
            child: ElevatedButton(
              onPressed: () {
                Clipboard.setData(ClipboardData(text: voucher['code'].toString()));
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Kode voucher disalin! Gunakan di keranjang.')));
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.black87,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 0),
                minimumSize: const Size(0, 30),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: const Text('Salin', style: TextStyle(fontSize: 10)),
            ),
          )
        ],
      ),
    );
  }
}
