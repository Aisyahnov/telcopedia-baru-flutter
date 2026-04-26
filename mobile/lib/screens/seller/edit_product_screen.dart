import 'package:flutter/material.dart';
import '../../models/category.dart';
import '../../models/product.dart';
import '../../services/seller_service.dart';
import 'package:google_fonts/google_fonts.dart';

class SellerEditProductScreen extends StatefulWidget {
  final Product product;
  const SellerEditProductScreen({super.key, required this.product});

  @override
  State<SellerEditProductScreen> createState() => _SellerEditProductScreenState();
}

class _SellerEditProductScreenState extends State<SellerEditProductScreen> {
  final _formKey = GlobalKey<FormState>();
  final SellerService _sellerService = SellerService();
  
  late TextEditingController _nameController;
  late TextEditingController _priceController;
  late TextEditingController _stockController;
  late TextEditingController _descriptionController;
  late TextEditingController _imageUrlController;
  
  int? _selectedCategoryId;
  String _selectedCondition = 'Very Good';
  List<Category> _categories = [];
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.product.name);
    _priceController = TextEditingController(text: widget.product.price.toInt().toString());
    _stockController = TextEditingController(text: widget.product.stock.toString());
    _descriptionController = TextEditingController(text: widget.product.description);
    _imageUrlController = TextEditingController(text: widget.product.imageUrl);
    _selectedCategoryId = widget.product.categoryId;
    _selectedCondition = widget.product.condition;
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    final cats = await _sellerService.getCategories();
    setState(() => _categories = cats);
  }

  Future<void> _save() async {
    if (_formKey.currentState!.validate() && _selectedCategoryId != null) {
      setState(() => _isSaving = true);
      
      final data = {
        'name': _nameController.text,
        'category_id': _selectedCategoryId,
        'condition': _selectedCondition,
        'price': double.parse(_priceController.text),
        'stock': int.parse(_stockController.text),
        'description': _descriptionController.text,
        'image_url': _imageUrlController.text,
      };

      final success = await _sellerService.updateProduct(widget.product.id, data);
      if (success && mounted) {
        Navigator.pop(context, true);
      } else if (mounted) {
        setState(() => _isSaving = false);
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal memperbarui produk.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Sunting Detail Barang'),
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
              _buildTextField(_nameController, 'Nama Barang', Icons.shopping_bag_outlined),
              
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
              _buildTextArea(_descriptionController, 'Deskripsi lengkap...'),

              const SizedBox(height: 30),
              _buildSectionTitle('Media & Visual'),
              _buildLabel('URL Foto Utama'),
              _buildTextField(_imageUrlController, 'URL Foto', Icons.camera_alt_outlined),
              
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
                  ),
                  child: _isSaving 
                    ? const CircularProgressIndicator(color: Colors.white)
                    : Text('SIMPAN PERUBAHAN 💾', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 50),
            ],
          ),
        ),
      ),
    );
  }

  // Reuse build helper methods from AddProductScreen (I'll copy them here for now, or you could refactor them into a separate widget/mixin later)
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
    return Padding(padding: const EdgeInsets.only(bottom: 8), child: Text(text, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade700)));
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
        prefixIcon: Container(padding: const EdgeInsets.all(15), child: Text('Rp', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey.shade500))),
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
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: Colors.grey.shade200)),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<int>(
          value: _selectedCategoryId,
          isExpanded: true,
          items: _categories.map((c) => DropdownMenuItem(value: c.id, child: Text(c.name, style: const TextStyle(fontSize: 14)))).toList(),
          onChanged: (val) => setState(() => _selectedCategoryId = val),
        ),
      ),
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
}
