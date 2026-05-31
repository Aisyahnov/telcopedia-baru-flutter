import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'package:flutter/services.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../models/product.dart';
import 'package:provider/provider.dart';
import '../../services/product_service.dart';
import '../../services/cart_service.dart';
import '../../services/favorite_service.dart';
import '../../services/auth_service.dart';
import '../../providers/auth_provider.dart';
import '../../models/chat.dart';
import 'chat_room_screen.dart';

class ProductDetailScreen extends StatefulWidget {
  final int productId;
  const ProductDetailScreen({super.key, required this.productId});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  final ProductService _productService = ProductService();
  final CartService _cartService = CartService();
  final FavoriteService _favoriteService = FavoriteService();
  final AuthService _authService = AuthService();

  Product? _product;
  List<Product> _relatedProducts = [];
  bool _isFavorited = false;
  bool _isLoading = true;
  int _quantity = 1;
  String _activeImageUrl = '';
  bool _isAddingToCart = false;

  @override
  void initState() {
    super.initState();
    _loadProductDetail();
  }

  Future<void> _loadProductDetail() async {
    setState(() => _isLoading = true);
    final data = await _productService.getProductDetail(widget.productId);
    if (data != null && mounted) {
      setState(() {
        _product = data['product'];
        _relatedProducts = List<Product>.from(data['related_products']);
        _isFavorited = data['is_favorited'] ?? false;
        _activeImageUrl = _product?.imageUrl ?? '';
        _isLoading = false;
        _quantity = 1;
      });
    } else if (mounted) {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Produk tidak ditemukan')));
      Navigator.pop(context);
    }
  }

  void _incrementQty() {
    if (_product != null && _quantity < _product!.stock) {
      setState(() => _quantity++);
    }
  }

  void _decrementQty() {
    if (_quantity > 1) {
      setState(() => _quantity--);
    }
  }

  Future<void> _addToCart({bool buyNow = false}) async {
    if (_product == null) return;
    final user = await _authService.getCurrentUser();
    if (user == null) {
      Navigator.pushNamed(context, '/login');
      return;
    }
    
    setState(() => _isAddingToCart = true);
    final success = await _cartService.addToCart(_product!.id, _quantity);
    setState(() => _isAddingToCart = false);
    
    if (mounted) {
      if (success) {
        if (buyNow) {
          Navigator.pushNamed(context, '/cart'); // Fallback direct checkout by going to cart
        } else {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Berhasil ditambahkan ke keranjang')));
        }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menambahkan ke keranjang')));
      }
    }
  }

  Future<void> _toggleFavorite() async {
    if (_product == null) return;
    final user = await _authService.getCurrentUser();
    if (user == null) {
      Navigator.pushNamed(context, '/login');
      return;
    }
    
    final success = await _favoriteService.toggleFavorite(_product!.id);
    if (success && mounted) {
      setState(() => _isFavorited = !_isFavorited);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(_isFavorited ? 'Ditambahkan ke Wishlist' : 'Dihapus dari Wishlist'),
        duration: const Duration(seconds: 1),
      ));
    }
  }

  Future<void> _shareWhatsApp() async {
    if (_product == null) return;
    final String text = 'Cek barang keren ini di Telcopedia: ${_product!.name} - ${ApiService.getImageUrl("product/${_product!.id}")}';
    final Uri url = Uri.parse('https://wa.me/?text=${Uri.encodeComponent(text)}');
    if (await canLaunchUrl(url)) {
      await launchUrl(url);
    }
  }

  void _copyLink() {
    if (_product == null) return;
    Clipboard.setData(ClipboardData(text: ApiService.getImageUrl('product/${_product!.id}')));
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Link produk berhasil disalin!')));
  }

  void _showEditQtyDialog() {
    if (_product == null) return;
    final controller = TextEditingController(text: _quantity.toString());
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Jumlah Pembelian', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Maksimal stok: ${_product!.stock}', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey)),
            const SizedBox(height: 10),
            TextField(
              controller: controller,
              keyboardType: TextInputType.number,
              autofocus: true,
              decoration: InputDecoration(
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                contentPadding: const EdgeInsets.symmetric(horizontal: 15),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () {
              final newQty = int.tryParse(controller.text) ?? _quantity;
              if (newQty > _product!.stock) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Stok tidak mencukupi (Maks: ${_product!.stock})')));
                return;
              }
              if (newQty < 1) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Minimal pembelian 1 item')));
                return;
              }
              setState(() => _quantity = newQty);
              Navigator.pop(context);
            },
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: Color(0xFFF8F9FA),
        body: Center(child: CircularProgressIndicator(color: Color(0xFF9F1521))),
      );
    }
    if (_product == null) return const SizedBox();

    final currencyFormatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    double avgRating = 0;
    if (_product!.reviews.isNotEmpty) {
      avgRating = _product!.reviews.map((e) => e.rating).reduce((a, b) => a + b) / _product!.reviews.length;
    }

    return Scaffold(
      backgroundColor: const Color(0xFFFCFCFC),
      appBar: AppBar(
        title: Text('Detail Produk', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 16)),
        backgroundColor: Colors.white,
        foregroundColor: const Color(0xFF9F1521),
        elevation: 0.5,
        actions: [
          IconButton(
            icon: const Icon(Icons.shopping_cart_outlined),
            onPressed: () => Navigator.pushNamed(context, '/cart'),
          )
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildGallery(),
            const SizedBox(height: 25),
            _buildProductInfo(avgRating),
            const SizedBox(height: 25),
            _buildBuyBox(currencyFormatter),
            const SizedBox(height: 25),
            _buildDescription(),
            const SizedBox(height: 25),
            _buildSellerProfile(),
            const SizedBox(height: 40),
            _buildReviews(),
            const SizedBox(height: 40),
            _buildRecommendations(currencyFormatter),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildGallery() {
    return Column(
      children: [
        Container(
          height: 300,
          width: double.infinity,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: const Color(0xFFF5F5F5)),
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(20),
            child: Image.network(_activeImageUrl, fit: BoxFit.contain, errorBuilder: (context, error, stackTrace) => const Icon(Icons.broken_image, color: Colors.grey, size: 50)),
          ),
        ),
        if (_product!.images.isNotEmpty) ...[
          const SizedBox(height: 15),
          SizedBox(
            height: 70,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: [_product!.imageUrl, ..._product!.images.map((e) => e.imageUrl)].length,
              itemBuilder: (context, index) {
                final url = index == 0 ? _product!.imageUrl! : _product!.images[index - 1].imageUrl;
                final isActive = _activeImageUrl == url;
                return GestureDetector(
                  onTap: () => setState(() => _activeImageUrl = url),
                  child: Container(
                    width: 70,
                    margin: const EdgeInsets.only(right: 10),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: isActive ? const Color(0xFF9F1521) : const Color(0xFFF0F0F0), width: 2),
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.network(url, fit: BoxFit.cover, errorBuilder: (context, error, stackTrace) => const Icon(Icons.broken_image, color: Colors.grey, size: 20)),
                    ),
                  ),
                );
              },
            ),
          ),
        ]
      ],
    );
  }

  Widget _buildProductInfo(double avgRating) {
    final isNew = _product!.condition.toLowerCase() == 'new' || _product!.condition.toLowerCase() == 'baru';
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: isNew ? Colors.blue.shade50 : Colors.orange.shade50,
                borderRadius: BorderRadius.circular(100),
              ),
              child: Text(
                _product!.condition.toUpperCase(),
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: isNew ? Colors.blue.shade700 : Colors.orange.shade800,
                  letterSpacing: 0.5,
                ),
              ),
            ),
            const SizedBox(width: 10),
            Icon(Icons.layers_outlined, size: 14, color: Colors.grey.shade500),
            const SizedBox(width: 4),
            Text(
              _product!.category?.name ?? 'Umum',
              style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade600),
            ),
          ],
        ),
        const SizedBox(height: 15),
        Text(
          _product!.name,
          style: GoogleFonts.plusJakartaSans(fontSize: 22, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A), letterSpacing: -0.5),
        ),
        const SizedBox(height: 15),
        Row(
          children: [
            if (_product!.reviews.isNotEmpty) ...[
              const Icon(Icons.star, color: Colors.amber, size: 18),
              const SizedBox(width: 4),
              Text(avgRating.toStringAsFixed(1), style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.amber)),
              const SizedBox(width: 8),
              Text('(${_product!.reviews.length} Ulasan)', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade600)),
            ] else
              Text('Belum ada ulasan', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade500, fontStyle: FontStyle.italic)),
          ],
        ),
        const SizedBox(height: 20),
        Text(
          NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(_product!.price),
          style: GoogleFonts.plusJakartaSans(fontSize: 26, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521), letterSpacing: -1),
        ),
        const SizedBox(height: 20),
        const Divider(color: Color(0xFFF0F0F0)),
      ],
    );
  }

  Widget _buildDescription() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text('Deskripsi Produk', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
        const SizedBox(height: 12),
        Text(
          _product!.description.isNotEmpty ? _product!.description : 'Tidak ada deskripsi untuk produk ini.',
          style: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey.shade700, height: 1.6),
        ),
        const SizedBox(height: 20),
        const Divider(color: Color(0xFFF0F0F0)),
      ],
    );
  }

  Widget _buildSellerProfile() {
    final sellerName = _product!.seller?.name ?? 'Penjual';
    final isVerified = _product!.seller?.isVerified ?? false;
    final joinDate = _product!.seller?.createdAt != null ? DateFormat('MMM yyyy').format(_product!.seller!.createdAt!) : '';

    return InkWell(
      onTap: () => Navigator.pushNamed(context, '/seller/profile', arguments: _product!.sellerId),
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: const Color(0xFFFAFAFA),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFF0F0F0)),
        ),
        child: Row(
          children: [
            CircleAvatar(
              radius: 25,
              backgroundColor: const Color(0xFF9F1521),
              backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(sellerName)}&background=9F1521&color=fff&bold=true'),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(sellerName, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
                      if (isVerified) ...[
                        const SizedBox(width: 4),
                        const Icon(Icons.check_circle, color: Colors.blue, size: 14),
                      ]
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text('Online • Bergabung $joinDate', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey.shade500)),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: Colors.grey, size: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildBuyBox(NumberFormat formatter) {
    return Container(
      padding: const EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFEEEEEE)),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 20, offset: const Offset(0, 10))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Ringkasan Belanja', style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
          const SizedBox(height: 20),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Stok Produk', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
              Text('${_product!.stock} Item', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
            ],
          ),
          const SizedBox(height: 15),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Subtotal', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
              Text(formatter.format(_product!.price * _quantity), style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
            ],
          ),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 20),
            child: Divider(color: Color(0xFFEEEEEE), height: 1),
          ),
          if (_product!.stock > 0) ...[
            Text('ATUR JUMLAH', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey.shade500, letterSpacing: 0.5)),
            const SizedBox(height: 10),
            Container(
              decoration: BoxDecoration(border: Border.all(color: const Color(0xFFEEEEEE)), borderRadius: BorderRadius.circular(10)),
              child: Row(
                children: [
                  IconButton(onPressed: _decrementQty, icon: const Icon(Icons.remove, size: 16)),
                  Expanded(
                    child: GestureDetector(
                      onTap: () => _showEditQtyDialog(),
                      child: Text('$_quantity', textAlign: TextAlign.center, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
                    ),
                  ),
                  IconButton(onPressed: _quantity < (_product?.stock ?? 0) ? _incrementQty : null, icon: Icon(Icons.add, size: 16, color: _quantity < (_product?.stock ?? 0) ? Colors.black : Colors.grey.shade300)),
                ],
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isAddingToCart ? null : () => _addToCart(buyNow: false),
                icon: _isAddingToCart ? const SizedBox() : const Icon(Icons.add_shopping_cart, size: 18),
                label: _isAddingToCart 
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) 
                    : const Text('Keranjang'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF9F1521),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 15),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                  textStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
                ),
              ),
            ),
            const SizedBox(height: 10),
            SizedBox(
              width: double.infinity,
              child: OutlinedButton(
                onPressed: _isAddingToCart ? null : () => _addToCart(buyNow: true),
                style: OutlinedButton.styleFrom(
                  foregroundColor: const Color(0xFF9F1521),
                  side: const BorderSide(color: Color(0xFF9F1521)),
                  padding: const EdgeInsets.symmetric(vertical: 15),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  textStyle: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold),
                ),
                child: const Text('Beli Sekarang'),
              ),
            ),
          ] else
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: null,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.grey.shade300,
                  padding: const EdgeInsets.symmetric(vertical: 15),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: Text('Stok Habis', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
              ),
            ),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () {
                    if (_product == null) return;
                    final authProvider = Provider.of<AuthProvider>(context, listen: false);
                    if (_product!.sellerId == authProvider.user?.id) {
                      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Ini adalah produk Anda sendiri')));
                      return;
                    }
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => ChatRoomScreen(
                          chat: Chat(
                            id: 0,
                            user1Id: authProvider.user?.id ?? 0,
                            user2Id: _product!.sellerId,
                            productId: _product!.id,
                            user2: _product!.seller,
                            product: _product!,
                            messages: [],
                          ),
                        ),
                      ),
                    );
                  },
                  icon: const Icon(Icons.chat_bubble_outline, size: 14),
                  label: Text('Chat', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold)),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.black87,
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _toggleFavorite,
                  icon: Icon(_isFavorited ? Icons.favorite : Icons.favorite_border, size: 14),
                  label: const Text('Wishlist'),
                  style: OutlinedButton.styleFrom(
                    backgroundColor: _isFavorited ? const Color(0xFFFFF5F5) : Colors.white,
                    foregroundColor: _isFavorited ? const Color(0xFF9F1521) : Colors.grey.shade700,
                    side: BorderSide(color: _isFavorited ? const Color(0xFF9F1521) : Colors.grey.shade300),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          const Divider(color: Color(0xFFEEEEEE), height: 1),
          const SizedBox(height: 20),
          Text('BAGIKAN PRODUK', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.w800, color: Colors.grey.shade500, letterSpacing: 0.5)),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _shareWhatsApp,
                  icon: const Icon(Icons.phone_android, size: 14), 
                  label: const Text('WhatsApp'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.green,
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _copyLink,
                  icon: const Icon(Icons.link, size: 14),
                  label: const Text('Copy Link'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.grey.shade800,
                    side: BorderSide(color: Colors.grey.shade300),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildReviews() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text('Ulasan Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
            const SizedBox(width: 15),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(color: const Color(0xFF9F1521).withValues(alpha: 0.1), borderRadius: BorderRadius.circular(100)),
              child: Text('${_product!.reviews.length} Review', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521))),
            ),
          ],
        ),
        const SizedBox(height: 25),
        if (_product!.reviews.isEmpty)
          Container(
            padding: const EdgeInsets.all(40),
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20), border: Border.all(color: const Color(0xFFEEEEEE))),
            alignment: Alignment.center,
            child: Column(
              children: [
                Icon(Icons.rate_review_outlined, size: 50, color: Colors.grey.shade300),
                const SizedBox(height: 15),
                Text('Belum ada ulasan untuk produk ini.', style: GoogleFonts.plusJakartaSans(color: Colors.grey.shade500)),
              ],
            ),
          )
        else
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _product!.reviews.length,
            separatorBuilder: (context, index) => const Padding(padding: EdgeInsets.symmetric(vertical: 20), child: Divider(color: Color(0xFFEEEEEE), height: 1)),
            itemBuilder: (context, index) {
              final review = _product!.reviews[index];
              return Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CircleAvatar(
                    radius: 20,
                    backgroundColor: const Color(0xFFF8F9FA),
                    backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(review.user?.name ?? 'U')}&background=F8F9FA&color=9F1521&bold=true'),
                  ),
                  const SizedBox(width: 15),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(review.user?.name ?? 'Pengguna', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
                        const SizedBox(height: 5),
                        Row(
                          children: [
                            Row(
                              children: List.generate(5, (starIndex) {
                                return Icon(
                                  Icons.star,
                                  size: 12,
                                  color: starIndex < review.rating ? Colors.amber : Colors.grey.shade300,
                                );
                              }),
                            ),
                            const SizedBox(width: 10),
                            if (review.createdAt != null)
                              Text(DateFormat('dd MMM yyyy').format(review.createdAt!), style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey.shade500)),
                          ],
                        ),
                        const SizedBox(height: 10),
                        Text(review.comment ?? 'Penjual tidak memberikan komentar teks.', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey.shade700)),
                      ],
                    ),
                  ),
                ],
              );
            },
          ),
      ],
    );
  }

  Widget _buildRecommendations(NumberFormat formatter) {
    if (_relatedProducts.isEmpty) return const SizedBox();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Divider(color: Color(0xFFEEEEEE)),
        const SizedBox(height: 25),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            RichText(
              text: TextSpan(
                style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A)),
                children: const [
                  TextSpan(text: 'Rekomendasi '),
                  TextSpan(text: 'Untuk Kamu', style: TextStyle(color: Color(0xFF9F1521))),
                ],
              ),
            ),
            GestureDetector(
              onTap: () {}, 
              child: Row(
                children: [
                  Text('Lihat Semua', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521))),
                  const SizedBox(width: 4),
                  const Icon(Icons.arrow_forward, size: 12, color: Color(0xFF9F1521)),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 20),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            childAspectRatio: 0.65,
            crossAxisSpacing: 15,
            mainAxisSpacing: 15,
          ),
          itemCount: _relatedProducts.length,
          itemBuilder: (context, index) {
            final rp = _relatedProducts[index];
            return GestureDetector(
              onTap: () => Navigator.pushReplacementNamed(context, '/product-detail', arguments: rp.id),
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
                      flex: 4,
                      child: Container(
                        width: double.infinity,
                        decoration: const BoxDecoration(
                          color: Color(0xFFF8F8F8),
                          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
                        ),
                        child: ClipRRect(
                          borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                          child: Image.network(rp.imageUrl ?? '', fit: BoxFit.cover, errorBuilder: (context, error, stackTrace) => const Icon(Icons.broken_image, color: Colors.grey)),
                        ),
                      ),
                    ),
                    Expanded(
                      flex: 5,
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(4)),
                              child: Text(rp.condition.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.bold, color: Colors.grey.shade600)),
                            ),
                            const SizedBox(height: 8),
                            Text(rp.name, maxLines: 2, overflow: TextOverflow.ellipsis, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w800, color: const Color(0xFF1A1A1A))),
                            const Spacer(),
                            Text(formatter.format(rp.price), style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ],
    );
  }
}
