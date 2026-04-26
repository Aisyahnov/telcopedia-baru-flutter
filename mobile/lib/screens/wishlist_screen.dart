import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../services/favorite_service.dart';
import '../services/cart_service.dart';
import '../models/product.dart';

class WishlistScreen extends StatefulWidget {
  const WishlistScreen({super.key});

  @override
  State<WishlistScreen> createState() => _WishlistScreenState();
}

class _WishlistScreenState extends State<WishlistScreen> {
  final FavoriteService _favoriteService = FavoriteService();
  final CartService _cartService = CartService();
  List<Product> _favorites = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadFavorites();
  }

  Future<void> _loadFavorites() async {
    final favs = await _favoriteService.getFavorites();
    if (mounted) {
      setState(() {
        _favorites = favs;
        _isLoading = false;
      });
    }
  }

  Future<void> _toggleFavorite(int productId) async {
    final success = await _favoriteService.toggleFavorite(productId);
    if (success) {
      _loadFavorites();
    }
  }

  Future<void> _addToCart(Product p) async {
    final success = await _cartService.addToCart(p.id, 1);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(success ? '${p.name} ditambah ke keranjang' : 'Gagal menambah ke keranjang'),
          backgroundColor: success ? Colors.green : Colors.red,
        ),
      );
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
          'Favorit Saya',
          style: GoogleFonts.plusJakartaSans(
            color: Colors.black,
            fontWeight: FontWeight.w900,
            fontSize: 20,
          ),
        ),
        centerTitle: true,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
          : _favorites.isEmpty
              ? _buildEmptyState()
              : _buildWishlistGrid(),
    );
  }

  Widget _buildWishlistGrid() {
    return RefreshIndicator(
      onRefresh: _loadFavorites,
      color: const Color(0xFF9F1521),
      child: GridView.builder(
        padding: const EdgeInsets.all(20),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          childAspectRatio: 0.65,
          crossAxisSpacing: 15,
          mainAxisSpacing: 15,
        ),
        itemCount: _favorites.length,
        itemBuilder: (context, index) {
          return _buildPremiumWishlistCard(_favorites[index]);
        },
      ),
    );
  }

  Widget _buildPremiumWishlistCard(Product p) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFF0F0F0)),
      ),
      child: Stack(
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                  child: Image.network(
                    p.imageUrl ?? '',
                    width: double.infinity,
                    fit: BoxFit.cover,
                    errorBuilder: (c, e, s) => Container(color: Colors.grey.shade100, child: const Icon(Icons.image_not_supported)),
                  ),
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
                    const SizedBox(height: 10),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        GestureDetector(
                          onTap: () => _addToCart(p),
                          child: Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(color: const Color(0xFF9F1521), borderRadius: BorderRadius.circular(8)),
                            child: const Icon(Icons.add_shopping_cart, color: Colors.white, size: 14),
                          ),
                        ),
                        GestureDetector(
                          onTap: () => Navigator.pushNamed(context, '/home/product', arguments: p.id),
                          child: Container(
                            padding: const EdgeInsets.all(6),
                            decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(8)),
                            child: const Icon(Icons.visibility_outlined, color: Colors.black54, size: 14),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
          Positioned(
            top: 10,
            right: 10,
            child: GestureDetector(
              onTap: () => _toggleFavorite(p.id),
              child: Container(
                padding: const EdgeInsets.all(6),
                decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle, boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 5)]),
                child: const Icon(Icons.favorite, color: Color(0xFF9F1521), size: 16),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(40),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.heart_broken_outlined, size: 80, color: Colors.grey.shade200),
            const SizedBox(height: 20),
            Text(
              'Wishlist Masih Kosong',
              style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 18),
            ),
            const SizedBox(height: 10),
            Text(
              'Cari barang bekas yang menarik perhatianmu dan klik ikon hati untuk menyimpannya di sini.',
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 14),
            ),
            const SizedBox(height: 30),
            ElevatedButton(
              onPressed: () => Navigator.pop(context),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF9F1521),
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
                padding: const EdgeInsets.symmetric(horizontal: 30, vertical: 12),
              ),
              child: const Text('Jelajahi Produk'),
            ),
          ],
        ),
      ),
    );
  }
}
