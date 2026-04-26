import 'package:flutter/material.dart';
import '../../models/voucher.dart';
import '../../services/admin_service.dart';
import '../../services/auth_service.dart';
import '../../models/user.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../widgets/admin_sidebar.dart';
import 'dashboard_screen.dart';
import 'products_screen.dart';
import 'users_screen.dart';
import 'payments_screen.dart';
import 'withdrawals_screen.dart';

class AdminVouchersScreen extends StatefulWidget {
  const AdminVouchersScreen({super.key});

  @override
  State<AdminVouchersScreen> createState() => _AdminVouchersScreenState();
}

class _AdminVouchersScreenState extends State<AdminVouchersScreen> {
  final AdminService _adminService = AdminService();
  final AuthService _authService = AuthService();
  List<Voucher> _vouchers = [];
  User? _user;
  bool _isLoading = true;

  final _codeController = TextEditingController();
  final _amountController = TextEditingController();
  DateTime? _selectedDate;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    final results = await Future.wait([
      _adminService.getVouchers(),
      _authService.getCurrentUser(),
    ]);
    
    if (mounted) {
      setState(() {
        _vouchers = (results[0] as List).map((json) => Voucher.fromJson(json)).toList();
        _user = results[1] as User?;
        _isLoading = false;
      });
    }
  }

  void _handleNavigation(String route) {
    Widget screen;
    switch (route) {
      case '/admin/dashboard': screen = const AdminDashboardScreen(); break;
      case '/admin/products': screen = const AdminProductsScreen(); break;
      case '/admin/users': screen = const AdminUsersScreen(); break;
      case '/admin/vouchers': return;
      case '/admin/payments': screen = const AdminPaymentsScreen(); break;
      case '/admin/withdrawals': screen = const AdminWithdrawalsScreen(); break;
      default: return;
    }
    Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => screen));
  }

  Future<void> _handleSubmit() async {
    if (_codeController.text.isEmpty || _amountController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Lengkapi form voucher!')));
      return;
    }

    final success = await _adminService.storeVoucher({
      'code': _codeController.text.toUpperCase(),
      'discount_amount': double.parse(_amountController.text),
      'valid_until': _selectedDate?.toIso8601String(),
    });

    if (success) {
      _codeController.clear();
      _amountController.clear();
      _selectedDate = null;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Voucher berhasil diterbitkan!'), backgroundColor: Colors.green));
      _loadData();
    }
  }

  @override
  Widget build(BuildContext context) {
    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: const Text('Kelola Voucher'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      drawer: AdminSidebar(
        user: _user,
        currentRoute: '/admin/vouchers',
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
                  const SizedBox(height: 25),
                  _buildVoucherForm(),
                  const SizedBox(height: 30),
                  Text('VOUCHER AKTIF', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey, letterSpacing: 1)),
                  const SizedBox(height: 15),
                  _buildVouchersList(currencyFormatter),
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
          'Manajemen Voucher',
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
        ),
        Text(
          'Terbitkan dan pantau kode promo diskon untuk mahasiswa.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildVoucherForm() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8)), child: const Icon(Icons.add, color: Color(0xFF9F1521), size: 16)),
              const SizedBox(width: 12),
              Text('Buat Voucher Baru', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
          const SizedBox(height: 20),
          _buildTextField('Kode Promo Eksklusif', 'TELKO50K', _codeController, icon: Icons.tag),
          const SizedBox(height: 15),
          _buildTextField('Potongan Belanja (Rp)', '50000', _amountController, icon: Icons.monetization_on_outlined, isNumber: true),
          const SizedBox(height: 15),
          Text('Batas Waktu (Expired)', style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey.shade600, letterSpacing: 0.5)),
          const SizedBox(height: 8),
          InkWell(
            onTap: () async {
              final date = await showDatePicker(context: context, initialDate: DateTime.now(), firstDate: DateTime.now(), lastDate: DateTime.now().add(const Duration(days: 365)));
              if (date != null) setState(() => _selectedDate = date);
            },
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
              decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade300), borderRadius: BorderRadius.circular(8)),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(_selectedDate == null ? 'Pilih Tanggal' : DateFormat('dd MMMM yyyy').format(_selectedDate!), style: TextStyle(color: _selectedDate == null ? Colors.grey : Colors.black, fontSize: 13)),
                  const Icon(Icons.calendar_today, size: 16, color: Colors.grey),
                ],
              ),
            ),
          ),
          const SizedBox(height: 25),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: _handleSubmit,
              icon: const Icon(Icons.print, size: 16),
              label: const Text('UNGGAH VOUCHER'),
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white, padding: const EdgeInsets.all(15), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)), textStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField(String label, String hint, TextEditingController controller, {IconData? icon, bool isNumber = false}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey.shade600, letterSpacing: 0.5)),
        const SizedBox(height: 8),
        TextField(
          controller: controller,
          keyboardType: isNumber ? TextInputType.number : TextInputType.text,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(color: Colors.grey.shade400, fontWeight: FontWeight.normal),
            prefixIcon: Icon(icon, size: 18, color: Colors.grey.shade400),
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: Colors.grey.shade300)),
            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: Colors.grey.shade300)),
            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFF9F1521))),
          ),
        ),
      ],
    );
  }

  Widget _buildVouchersList(NumberFormat formatter) {
    if (_vouchers.isEmpty) {
      return Center(
        child: Column(
          children: [
            const SizedBox(height: 30),
            Icon(Icons.confirmation_number_outlined, size: 60, color: Colors.grey.withOpacity(0.2)),
            const SizedBox(height: 10),
            Text('Belum ada promo aktif.', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey.shade400)),
          ],
        ),
      );
    }

    return Container(
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10, offset: const Offset(0, 4))]),
      child: ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: _vouchers.length,
        separatorBuilder: (context, index) => const Divider(height: 1, color: Color(0xFFF1F1F1)),
        itemBuilder: (context, index) {
          final v = _vouchers[index];
          return ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            leading: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(color: Colors.orange.withOpacity(0.1), borderRadius: BorderRadius.circular(4), border: Border.all(color: Colors.orange.withOpacity(0.5))),
              child: Text(v.code, style: const TextStyle(fontFamily: 'monospace', fontWeight: FontWeight.w900, color: Colors.orange, fontSize: 13)),
            ),
            title: Text(formatter.format(v.discountAmount), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w900, fontSize: 15, color: const Color(0xFF9F1521))),
            trailing: v.validUntil != null 
              ? Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('BERLAKU HINGGA', style: GoogleFonts.plusJakartaSans(fontSize: 7, fontWeight: FontWeight.bold, color: Colors.grey)),
                    Text(DateFormat('dd MMM yyyy').format(v.validUntil!), style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
                  ],
                )
              : Container(padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2), decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.grey.shade300)), child: const Text('FOREVER', style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey))),
          );
        },
      ),
    );
  }
}
