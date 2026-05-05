import 'package:flutter/material.dart';
import '../../models/category.dart';
import '../../services/seller_service.dart';
import 'package:google_fonts/google_fonts.dart';

class SellerAddProductScreen extends StatefulWidget {
  const SellerAddProductScreen({super.key});

  @override
  State<SellerAddProductScreen> createState() => _SellerAddProductScreenState();
}

class _SellerAddProductScreenState extends State<SellerAddProductScreen> {
  final _formKey = GlobalKey<FormState>();
  final SellerService _sellerService = SellerService();
  
  final _nameController = TextEditingController();
  final _priceController = TextEditingController();
  final _stockController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _imageUrlController = TextEditingController(); // Untuk demo, kita pakai URL
  
  int? _selectedCategoryId;
  int? _selectedSubCategoryId;
  String _selectedCondition = 'Very Good';
  List<Category> _categories = [];
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    final cats = await _sellerService.getCategories();
    setState(() => _categories = cats);
  }

  Future<void> _save() async {
    final mainCat = _categories.firstWhere((c) => c.id == _selectedCategoryId, orElse: () => _categories.first);
    final hasSub = mainCat.subCategories != null && mainCat.subCategories!.isNotEmpty;

    if (_formKey.currentState!.validate() && _selectedCategoryId != null) {
      if (hasSub && _selectedSubCategoryId == null) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Silakan pilih sub-kategori.')));
        return;
      }
      
      setState(() => _isSaving = true);
      
      final data = {
        'name': _nameController.text,
        'category_id': _selectedSubCategoryId ?? _selectedCategoryId,
        'condition': _selectedCondition,
        'price': double.parse(_priceController.text),
        'stock': int.parse(_stockController.text),
        'description': _descriptionController.text,
        'image_url': _imageUrlController.text, // Nanti bisa diganti dengan upload asli
      };

      final success = await _sellerService.storeProduct(data);
      if (success && mounted) {
        Navigator.pop(context, true);
      } else if (mounted) {
        setState(() => _isSaving = false);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyimpan produk.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Tambah Produk Baru'),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF1A1A1A),
        elevation: 0.5,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(25),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildSectionTitle('Informasi Dasar'),
              _buildLabel('Nama Barang / Judul Iklan'),
              _buildTextField(_nameController, 'Contoh: MacBook Pro 2020 M1', Icons.shopping_bag_outlined),
              
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('Kategori'),
                        _buildCategoryDropdown(),
                      ],
                    ),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('Kondisi'),
                        _buildConditionDropdown(),
                      ],
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('Harga Jual'),
                        _buildPriceField(),
                      ],
                    ),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildLabel('Stok (Pcs)'),
                        _buildTextField(_stockController, '1', Icons.inventory_2_outlined, isNumber: true),
                      ],
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 30),
              _buildSectionTitle('Deskripsi Produk'),
              _buildLabel('Deskripsi Lengkap'),
              _buildTextArea(_descriptionController, 'Jelaskan detail spesifikasi...'),

              const SizedBox(height: 30),
              _buildSectionTitle('Media & Visual'),
              _buildLabel('URL Foto Utama'),
              _buildTextField(_imageUrlController, 'https://example.com/image.jpg', Icons.camera_alt_outlined),
              
              const SizedBox(height: 25),
              _buildTipCard(),
              
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                height: 55,
                child: ElevatedButton(
                  onPressed: _isSaving ? null : _save,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF9F1521),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                    elevation: 5,
                  ),
                  child: _isSaving 
                    ? const CircularProgressIndicator(color: Colors.white)
                    : Text('UNGGAH PRODUK 🚀', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 50),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      child: Row(
        children: [
          Text(title.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521), letterSpacing: 1.5)),
          const SizedBox(width: 15),
          const Expanded(child: Divider(color: Color(0xFFF1F1F1))),
        ],
      ),
    );
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700)),
    );
  }

  Widget _buildTextField(TextEditingController controller, String hint, IconData icon, {bool isNumber = false}) {
    return TextFormField(
      controller: controller,
      keyboardType: isNumber ? TextInputType.number : TextInputType.text,
      decoration: InputDecoration(
        hintText: hint,
        prefixIcon: Icon(icon, color: const Color(0xFF9F1521), size: 20),
        filled: true,
        fillColor: Colors.grey.shade50,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      ),
      validator: (value) => value!.isEmpty ? 'Wajib diisi' : null,
    );
  }

  Widget _buildPriceField() {
    return TextFormField(
      controller: _priceController,
      keyboardType: TextInputType.number,
      decoration: InputDecoration(
        hintText: '0',
        prefixIcon: Container(
          padding: const EdgeInsets.all(15),
          child: Text('Rp', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey.shade500)),
        ),
        filled: true,
        fillColor: Colors.grey.shade50,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
      ),
      validator: (value) => value!.isEmpty ? 'Wajib diisi' : null,
    );
  }

  Widget _buildTextArea(TextEditingController controller, String hint) {
    return TextFormField(
      controller: controller,
      maxLines: 5,
      decoration: InputDecoration(
        hintText: hint,
        filled: true,
        fillColor: Colors.grey.shade50,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide(color: Colors.grey.shade200)),
      ),
      validator: (value) => value!.isEmpty ? 'Wajib diisi' : null,
    );
  }

  Widget _buildCategoryDropdown() {
    final mainCategories = _categories;
    final selectedMainCat = mainCategories.firstWhere((c) => c.id == _selectedCategoryId, orElse: () => mainCategories.isNotEmpty ? mainCategories.first : Category(id: 0, name: '', slug: ''));
    final subCategories = selectedMainCat.subCategories ?? [];

    return Column(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<int>(
              value: _selectedCategoryId,
              isExpanded: true,
              hint: const Text('Pilih Kategori', style: TextStyle(fontSize: 14)),
              items: mainCategories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name, style: const TextStyle(fontSize: 14)))).toList(),
              onChanged: (val) {
                setState(() {
                  _selectedCategoryId = val;
                  _selectedSubCategoryId = null; // Reset sub
                });
              },
            ),
          ),
        ),
        if (subCategories.isNotEmpty) ...[
          const SizedBox(height: 10),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
            child: DropdownButtonHideUnderline(
              child: DropdownButton<int>(
                value: _selectedSubCategoryId,
                isExpanded: true,
                hint: const Text('Pilih Sub-Kategori', style: TextStyle(fontSize: 14)),
                items: subCategories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name, style: const TextStyle(fontSize: 14)))).toList(),
                onChanged: (val) => setState(() => _selectedSubCategoryId = val),
              ),
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildConditionDropdown() {
    final conditions = ['New', 'Like New', 'Very Good', 'Good', 'Pre-Loved'];
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: _selectedCondition,
          isExpanded: true,
          items: conditions.map((c) => DropdownMenuItem(value: c, child: Text(c, style: const TextStyle(fontSize: 14)))).toList(),
          onChanged: (val) => setState(() => _selectedCondition = val!),
        ),
      ),
    );
  }

  Widget _buildTipCard() {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: const Color(0xFFFFF9F0), borderRadius: BorderRadius.circular(15), border: Border.all(color: const Color(0xFFFFE8CC))),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Icon(Icons.lightbulb_outline, color: Colors.orange, size: 20),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Tips Jualan Cepat', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.orange.shade900)),
                const SizedBox(height: 4),
                Text('Gunakan foto dengan pencahayaan terang dan latar belakang bersih.', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade700)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
