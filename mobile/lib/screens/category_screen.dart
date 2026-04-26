import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../services/product_service.dart';
import '../models/product.dart';
import '../models/category.dart' as model;

class CategoryScreen extends StatefulWidget {
  const CategoryScreen({super.key});

  @override
  State<CategoryScreen> createState() => _CategoryScreenState();
}

class _CategoryScreenState extends State<CategoryScreen> {
  final ProductService _productService = ProductService();
  List<model.Category> _categories = [];
  List<Product> _products = [];
  int? _selectedCategoryId;
  bool _isLoadingCategories = true;
  bool _isLoadingProducts = false;

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  Future<void> _loadCategories() async {
    final cats = await _productService.getCategories();
    if (mounted) {
      setState(() {
        _categories = cats;
        _isLoadingCategories = false;
        if (_categories.isNotEmpty) {
          _selectedCategoryId = _categories[0].id;
          _loadProducts(_selectedCategoryId!);
        }
      });
    }
  }

  Future<void> _loadProducts(int categoryId) async {
    setState(() => _isLoadingProducts = true);
    final products = await _productService.getProducts(categoryId: categoryId);
    if (mounted) {
      setState(() {
        _products = products;
        _isLoadingProducts = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFCFCFC),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        title: Text(
          'Eksplorasi Kategori',
          style: GoogleFonts.plusJakartaSans(
            color: Colors.black,
            fontWeight: FontWeight.w900,
            fontSize: 20,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          const SizedBox(height: 10),
          _buildCategoryNav(),
          Expanded(
            child: _isLoadingProducts 
              ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
              : _buildProductGrid(),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryNav() {
    if (_isLoadingCategories) {
      return const SizedBox(height: 60, child: Center(child: LinearProgressIndicator()));
    }
    return SizedBox(
      height: 60,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 15),
        itemCount: _categories.length,
        itemBuilder: (context, index) {
          final cat = _categories[index];
          final isSelected = _selectedCategoryId == cat.id;
          return Padding(
            padding: const EdgeInsets.only(right: 10),
            child: ChoiceChip(
              label: Text(cat.name),
              selected: isSelected,
              onSelected: (selected) {
                if (selected) {
                  setState(() => _selectedCategoryId = cat.id);
                  _loadProducts(cat.id);
                }
              },
              selectedColor: const Color(0xFF9F1521),
              backgroundColor: Colors.white,
              labelStyle: GoogleFonts.plusJakartaSans(
                color: isSelected ? Colors.white : Colors.black87,
                fontWeight: FontWeight.bold,
                fontSize: 13,
              ),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(100),
                side: BorderSide(color: isSelected ? const Color(0xFF9F1521) : Colors.grey.shade200),
              ),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              showCheckmark: false,
            ),
          );
        },
      ),
    );
  }

  Widget _buildProductGrid() {
    if (_products.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.inventory_2_outlined, size: 64, color: Colors.grey.shade300),
            const SizedBox(height: 16),
            Text(
              'Belum ada produk di kategori ini.',
              style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 14),
            ),
          ],
        ),
      );
    }

    return GridView.builder(
      padding: const EdgeInsets.all(20),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.65,
        crossAxisSpacing: 15,
        mainAxisSpacing: 15,
      ),
      itemCount: _products.length,
      itemBuilder: (context, index) {
        return _buildPremiumProductCard(_products[index]);
      },
    );
  }

  Widget _buildPremiumProductCard(Product p) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return GestureDetector(
      onTap: () => Navigator.pushNamed(context, '/product-detail', arguments: p.id),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFF0F0F0)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Stack(
                children: [
                  ClipRRect(
                    borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                    child: Image.network(
                      p.imageUrl ?? '',
                      width: double.infinity,
                      fit: BoxFit.cover,
                      errorBuilder: (c, e, s) => Container(color: Colors.grey.shade100, child: const Icon(Icons.image_not_supported, color: Colors.grey)),
                    ),
                  ),
                  Positioned(
                    top: 10,
                    right: 10,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.9),
                        borderRadius: BorderRadius.circular(100),
                      ),
                      child: Text(
                        p.category?.name ?? 'General',
                        style: GoogleFonts.plusJakartaSans(
                          color: const Color(0xFF9F1521),
                          fontSize: 8,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    p.name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 12, height: 1.2),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    formatter.format(p.price),
                    style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFF9F1521),
                      fontWeight: FontWeight.w900,
                      fontSize: 14,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 8,
                        backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(p.seller?.name ?? "S")}&background=F8F9FA&color=9F1521'),
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          p.seller?.name?.split(' ')[0] ?? 'Seller',
                          style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.bold),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
