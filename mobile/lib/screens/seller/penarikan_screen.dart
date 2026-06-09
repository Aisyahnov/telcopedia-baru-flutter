import 'package:flutter/material.dart';
import '../../models/penarikan_dana.dart';
import '../../models/user.dart';
import '../../services/seller_service.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:flutter/services.dart';
import '../../utils/currency_formatter.dart';

class SellerPenarikanDanaScreen extends StatefulWidget {
  const SellerPenarikanDanaScreen({super.key});

  @override
  State<SellerPenarikanDanaScreen> createState() => _SellerPenarikanDanaScreenState();
}

class _SellerPenarikanDanaScreenState extends State<SellerPenarikanDanaScreen> {
  final SellerService _sellerService = SellerService();
  final AuthService _authService = AuthService();
  List<PenarikanDana> _penarikan = [];
  User? _user;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final user = await _authService.getCurrentUser();
    final penarikan = await _sellerService.getPenarikanDanas();
    if (mounted) {
      setState(() {
        _user = user;
        _penarikan = penarikan;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/penarikan') return;
    if (route == '/seller/settings') {
      final authId = Provider.of<AuthProvider>(context, listen: false).user?.id;
      Navigator.pushNamed(context, route, arguments: _user?.id ?? authId);
      return;
    }
    Navigator.pushNamed(context, route);
  }

  @override
  Widget build(BuildContext context) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Saldo & Penarikan'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: SellerSidebar(
        user: _user,
        currentRoute: '/seller/penarikan',
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
                  _buildBalanceCard(formatter),
                  const SizedBox(height: 30),
                  Text('RIWAYAT PENARIKAN DANA', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey.shade600, letterSpacing: 1)),
                  const SizedBox(height: 15),
                  _buildPenarikanDanaList(formatter),
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
        Text('Manajemen Saldo', style: GoogleFonts.plusJakartaSans(fontSize: 20, fontWeight: FontWeight.w900)),
        Text('Kelola pendapatan dan riwayat pencairan dana.', style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600)),
      ],
    );
  }

  Widget _buildBalanceCard(NumberFormat formatter) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(25),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: const Border(left: BorderSide(color: Color(0xFF9F1521), width: 5)),
            boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 15, offset: const Offset(0, 5))],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('SALDO TERSEDIA', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                  const SizedBox(height: 5),
                  Text(formatter.format(_user?.saldo ?? 0), style: GoogleFonts.plusJakartaSans(fontSize: 24, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A))),
                  const SizedBox(height: 5),
                  Text('Dapat dicairkan kapan saja.', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey)),
                ],
              ),
              Container(
                padding: const EdgeInsets.all(15),
                decoration: BoxDecoration(color: const Color(0xFF9F1521).withValues(alpha: 0.05), shape: BoxShape.circle),
                child: const Icon(Icons.account_balance_wallet_outlined, color: Color(0xFF9F1521), size: 30),
              ),
            ],
          ),
        ),
        const SizedBox(height: 20),
        SizedBox(
          width: double.infinity,
          height: 55,
          child: ElevatedButton.icon(
            onPressed: () => _showWithdrawForm(),
            icon: const Icon(Icons.send_outlined, size: 18),
            label: Text('AJUKAN PENARIKAN DANA', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF9F1521),
              foregroundColor: Colors.white,
              elevation: 5,
              shadowColor: const Color(0xFF9F1521).withValues(alpha: 0.3),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildPenarikanDanaList(NumberFormat formatter) {
    if (_penarikan.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.symmetric(vertical: 40),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
        child: Column(
          children: [
            Icon(Icons.history_outlined, size: 40, color: Colors.grey.shade300),
            const SizedBox(height: 10),
            Text('Belum ada riwayat penarikan.', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 12)),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _penarikan.length,
        separatorBuilder: (context, index) => Divider(color: Colors.grey.shade100, height: 1),
        itemBuilder: (context, index) {
          final w = _penarikan[index];
          return _buildPenarikanDanaItem(w, formatter);
        },
      ),
    );
  }

  Widget _buildPenarikanDanaItem(PenarikanDana w, NumberFormat formatter) {
    return Padding(
      padding: const EdgeInsets.all(15),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(formatter.format(w.amount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, color: Colors.red, fontSize: 14)),
                const SizedBox(height: 4),
                Text(DateFormat('dd MMM YYYY').format(w.createdAt), style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
              ],
            ),
          ),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(w.bankName, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.bold)),
                Text('${w.accountNumber} a/n ${w.accountName}', style: GoogleFonts.plusJakartaSans(fontSize: 9, color: Colors.grey), maxLines: 1, overflow: TextOverflow.ellipsis),
              ],
            ),
          ),
          _buildStatusBadge(w.status),
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
        label = 'PENDING';
        break;
      case 'approved':
        color = Colors.green;
        bgColor = Colors.green.shade50;
        label = 'BERHASIL';
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
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(6), border: Border.all(color: color.withValues(alpha: 0.3))),
      child: Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: color)),
    );
  }

  void _showWithdrawForm() {
    final amountController = TextEditingController();
    final bankController = TextEditingController();
    final numberController = TextEditingController();
    final nameController = TextEditingController();
    bool isSubmitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => Container(
          decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(25))),
          padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom, left: 25, right: 25, top: 25),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
                const SizedBox(height: 20),
                Text('Tarik Dana', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                const SizedBox(height: 20),
                
                // Balance Banner
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(color: const Color(0xFF9F1521).withValues(alpha: 0.05), borderRadius: BorderRadius.circular(20), border: Border.all(color: const Color(0xFF9F1521).withValues(alpha: 0.1))),
                  child: Column(
                    children: [
                      Text('SALDO ANDA SAAT INI', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521))),
                      const SizedBox(height: 5),
                      Text(NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(_user?.saldo ?? 0), style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                    ],
                  ),
                ),
                const SizedBox(height: 25),
                
                _buildLabel('JUMLAH PENARIKAN (MIN RP 10.000)'),
                TextField(
                  controller: amountController,
                  keyboardType: TextInputType.number,
                  inputFormatters: [CurrencyInputFormatter()],
                  decoration: InputDecoration(prefixText: 'Rp ', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none)),
                ),
                const SizedBox(height: 20),
                
                _buildLabel('BANK / E-WALLET'),
                TextField(
                  controller: bankController,
                  decoration: InputDecoration(hintText: 'BCA, BNI, GoPay...', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none)),
                ),
                const SizedBox(height: 20),
                
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildLabel('NOMOR REKENING'),
                          TextField(
                            controller: numberController,
                            keyboardType: TextInputType.number,
                            decoration: InputDecoration(hintText: '000111...', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none)),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 15),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildLabel('ATAS NAMA'),
                          TextField(
                            controller: nameController,
                            decoration: InputDecoration(hintText: 'Sesuai buku...', filled: true, fillColor: Colors.grey.shade50, border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide.none)),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                
                const SizedBox(height: 25),
                // Info
                Container(
                  padding: const EdgeInsets.all(15),
                  decoration: BoxDecoration(color: Colors.orange.shade50, borderRadius: BorderRadius.circular(15)),
                  child: Row(
                    children: [
                      const Icon(Icons.info_outline, color: Colors.orange, size: 20),
                      const SizedBox(width: 12),
                      Expanded(child: Text('PROSES PENARIKAN MEMAKAN WAKTU 1-2 HARI KERJA.', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.orange.shade900))),
                    ],
                  ),
                ),
                const SizedBox(height: 30),
                
                SizedBox(
                  width: double.infinity,
                  height: 55,
                  child: ElevatedButton(
                    onPressed: isSubmitting ? null : () async {
                      setModalState(() => isSubmitting = true);
                      final success = await _sellerService.requestPenarikanDana({
                        'amount': double.parse(amountController.text.replaceAll('.', '')),
                        'bank_name': bankController.text,
                        'account_number': numberController.text,
                        'account_name': nameController.text,
                      });
                      if (!mounted) return;
                      if (success) {
                        Navigator.pop(context);
                        _loadData();
                      } else {
                        setModalState(() => isSubmitting = false);
                      }
                    },
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30))),
                    child: isSubmitting ? const CircularProgressIndicator(color: Colors.white, strokeWidth: 2) : const Text('KIRIM PERMINTAAN 🚀', style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Padding(padding: const EdgeInsets.only(bottom: 8), child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)));
  }
}
