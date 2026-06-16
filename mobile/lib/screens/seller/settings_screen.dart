import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'package:flutter/foundation.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../../models/user.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';

class SellerSettingsScreen extends StatefulWidget {
  const SellerSettingsScreen({super.key});

  @override
  State<SellerSettingsScreen> createState() => _SellerSettingsScreenState();
}

class _SellerSettingsScreenState extends State<SellerSettingsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final AuthService _authService = AuthService();
  final ImagePicker _picker = ImagePicker();
  final _formKey = GlobalKey<FormState>();
  final _passwordFormKey = GlobalKey<FormState>();

  // Profile controllers
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();

  // Password controllers
  final _currentPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  XFile? _avatarFile;
  XFile? _ktmFile;
  bool _isLoading = true;
  bool _isSaving = false;
  User? _user;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    final user = await _authService.getCurrentUser();
    if (mounted && user != null) {
      setState(() {
        _user = user;
        _nameController.text = user.name;
        _phoneController.text = user.phone ?? '';
        _addressController.text = user.address ?? '';
        _isLoading = false;
      });
    }
  }

  Future<void> _pickImage(bool isAvatar) async {
    final XFile? image = await _picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() {
        if (isAvatar) {
          _avatarFile = image;
        } else {
          _ktmFile = image;
        }
      });
    }
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isSaving = true);
    
    try {
      // 1. Upload Photo if changed
      if (_avatarFile != null) {
        await _authService.updatePhoto(_avatarFile!);
      }

      // 2. Upload KTM if changed
      if (_ktmFile != null) {
        await _authService.updateKtm(_ktmFile!);
      }

      // 3. Update Text Info
      final Map<String, dynamic> data = {
        'name': _nameController.text,
        'phone': _phoneController.text,
        'address': _addressController.text,
      };

      final success = await _authService.updateProfile(data);
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Profil berhasil diperbarui!'), backgroundColor: Colors.green),
        );
        _loadUserData();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memperbarui profil: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  Future<void> _updatePassword() async {
    if (!_passwordFormKey.currentState!.validate()) return;

    setState(() => _isSaving = true);
    
    try {
      final success = await _authService.updatePassword(
        _currentPasswordController.text,
        _newPasswordController.text,
      );

      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Password berhasil diperbarui!'), backgroundColor: Colors.green),
        );
        _currentPasswordController.clear();
        _newPasswordController.clear();
        _confirmPasswordController.clear();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal memperbarui password: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      appBar: AppBar(
        title: Text(_user?.role == 'seller' ? 'Pengaturan Toko' : 'Pengaturan Profil', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
        bottom: TabBar(
          controller: _tabController,
          labelColor: const Color(0xFF9F1521),
          unselectedLabelColor: Colors.grey,
          indicatorColor: const Color(0xFF9F1521),
          tabs: const [
            Tab(text: 'Profil'),
            Tab(text: 'Keamanan'),
          ],
        ),
      ),
      drawer: _user?.role == 'seller' ? SellerSidebar(
        user: _user,
        currentRoute: '/seller/settings',
        onNavigate: (route) {
          if (route == '/seller/settings') return;
          Navigator.pushReplacementNamed(context, route);
        },
        onLogout: () async {
          await _authService.logout();
          if (!mounted) return;
          Navigator.pushReplacementNamed(context, '/login');
        },
      ) : null,
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
        : TabBarView(
            controller: _tabController,
            children: [
              _buildProfileTab(),
              _buildSecurityTab(),
            ],
          ),
    );
  }

  Widget _buildProfileTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionHeader('Informasi Dasar', Icons.person_outline),
            const SizedBox(height: 20),

            Center(
              child: Stack(
                children: [
                  Container(
                    width: 120,
                    height: 120,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 4),
                      boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.1), blurRadius: 15)],
                      image: DecorationImage(
                        fit: BoxFit.cover,
                        image: _avatarFile != null
                            ? (kIsWeb ? NetworkImage(_avatarFile!.path) : FileImage(File(_avatarFile!.path))) as ImageProvider
                            : (_user?.photo != null 
                                ? NetworkImage(ApiService.getImageUrl('storage/${_user!.photo}'))
                                : NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(_user?.name ?? "")}&background=9F1521&color=fff&size=200') as ImageProvider),
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: GestureDetector(
                      onTap: () => _pickImage(true),
                      child: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: const Color(0xFF9F1521),
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.white, width: 2),
                        ),
                        child: const Icon(Icons.camera_alt, color: Colors.white, size: 18),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 30),

            _buildLabel('Nama Lengkap'),
            _buildTextField(_nameController, 'Masukkan nama lengkap', Icons.person_outline),
            
            const SizedBox(height: 20),
            _buildLabel('Nomor WhatsApp'),
            _buildTextField(_phoneController, '08xxxxxxxx', Icons.phone_android_outlined, keyboardType: TextInputType.phone),
            
            const SizedBox(height: 20),
            _buildLabel('Alamat / Lokasi Lapak'),
            _buildTextField(_addressController, 'Detail alamat...', Icons.location_on_outlined, maxLines: 3),
            
            const SizedBox(height: 30),
            _buildSectionHeader('Verifikasi Identitas (KTM)', Icons.contact_page_outlined),
            const SizedBox(height: 15),
            
            GestureDetector(
              onTap: () => _pickImage(false),
              child: Container(
                width: double.infinity,
                height: 180,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.grey.shade200),
                  boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.02), blurRadius: 10)],
                ),
                child: _ktmFile != null 
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(20), 
                      child: kIsWeb 
                        ? Image.network(_ktmFile!.path, fit: BoxFit.cover) 
                        : Image.network(_ktmFile!.path, fit: BoxFit.cover)
                    )
                  : (_user?.ktm != null 
                      ? ClipRRect(borderRadius: BorderRadius.circular(20), child: Image.network(ApiService.getImageUrl('api/storage/${_user!.ktm}'), fit: BoxFit.cover, errorBuilder: (c, e, s) => const Icon(Icons.contact_page_outlined, size: 40, color: Colors.grey)))
                      : Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.add_a_photo_outlined, size: 40, color: Colors.grey.shade400),
                            const SizedBox(height: 10),
                            Text('Upload Foto KTM', style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontWeight: FontWeight.bold)),
                          ],
                        )),
              ),
            ),
            
            const SizedBox(height: 40),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: _isSaving ? null : _saveProfile,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF9F1521),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 0,
                ),
                child: _isSaving 
                  ? const CircularProgressIndicator(color: Colors.white)
                  : Text('SIMPAN PERUBAHAN', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
              ),
            ),
            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  Widget _buildSecurityTab() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Form(
        key: _passwordFormKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionHeader('Keamanan Akun', Icons.lock_outline),
            const SizedBox(height: 10),
            Text(
              'Ganti kata sandi secara berkala untuk menjaga keamanan akun Anda.',
              style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey),
            ),
            const SizedBox(height: 30),

            _buildLabel('Kata Sandi Saat Ini'),
            _buildTextField(_currentPasswordController, '********', Icons.vpn_key_outlined, isPassword: true),
            
            const SizedBox(height: 20),
            _buildLabel('Kata Sandi Baru'),
            _buildTextField(_newPasswordController, '********', Icons.lock_open_outlined, isPassword: true),
            
            const SizedBox(height: 20),
            _buildLabel('Konfirmasi Kata Sandi Baru'),
            _buildTextField(_confirmPasswordController, '********', Icons.lock_reset_outlined, isPassword: true),
            
            const SizedBox(height: 40),
            SizedBox(
              width: double.infinity,
              height: 55,
              child: ElevatedButton(
                onPressed: _isSaving ? null : _updatePassword,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1A1A1A),
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                  elevation: 0,
                ),
                child: _isSaving 
                  ? const CircularProgressIndicator(color: Colors.white)
                  : Text('UPDATE PASSWORD', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title, IconData icon) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: const Color(0xFF9F1521).withValues(alpha: 0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: const Color(0xFF9F1521), size: 20),
        ),
        const SizedBox(width: 12),
        Text(title, style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w800)),
      ],
    );
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, left: 4),
      child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
    );
  }

  Widget _buildTextField(TextEditingController controller, String hint, IconData icon, {bool isPassword = false, TextInputType? keyboardType, int maxLines = 1}) {
    return TextFormField(
      controller: controller,
      obscureText: isPassword,
      keyboardType: keyboardType,
      maxLines: maxLines,
      style: GoogleFonts.plusJakartaSans(fontSize: 14),
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: Icon(icon, size: 20, color: const Color(0xFF9F1521).withValues(alpha: 0.7)),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Colors.grey.shade200)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: BorderSide(color: Colors.grey.shade200)),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(15), borderSide: const BorderSide(color: Color(0xFF9F1521))),
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      ),
      validator: (val) => val == null || val.isEmpty ? 'Field ini wajib diisi' : null,
    );
  }
}
