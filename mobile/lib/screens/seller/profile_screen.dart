import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/product.dart';
import '../../models/user.dart';
import '../../models/review.dart';
import '../../services/product_service.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import '../../services/auth_service.dart';
import '../../widgets/seller_sidebar.dart';
import '../../providers/auth_provider.dart';
class SellerProfileScreen extends StatefulWidget {
  final int sellerId;
  const SellerProfileScreen({super.key, required this.sellerId});

  @override
  State<SellerProfileScreen> createState() => _SellerProfileScreenState();
}

class _SellerProfileScreenState extends State<SellerProfileScreen> {
  final ProductService _productService = ProductService();
  final AuthService _authService = AuthService();
  User? _seller;
  User? _currentUser;
  List<Product> _products = [];
  List<Review> _sellerReviews = [];
  bool _isLoading = true;
  double _rating = 0.0;
  bool _isProductTab = true;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    try {
      final user = await _authService.getCurrentUser();
      final data = await _productService.getSellerProfile(widget.sellerId);
      
      if (mounted) {
        setState(() {
          _currentUser = user;
          if (data != null) {
            _seller = User.fromJson(data['seller']);
            final List prodData = data['products']['data'];
            _products = prodData.map((json) => Product.fromJson(json)).toList();
            
            if (data['reviews'] != null && data['reviews']['data'] != null) {
              final List revData = data['reviews']['data'];
              _sellerReviews = revData.map((json) => Review.fromJson(json)).toList();
            }
            
            _rating = (data['rating'] ?? 0.0).toDouble();
          }
          _isLoading = false;
        });
      }
    } catch (e) {
      debugPrint('Error loading profile: $e');
      if (mounted) {
        setState(() => _isLoading = false);
      }
    }
  }

  void _handleNavigation(String route) {
    if (route == '/seller/profile') return;
    
    Navigator.pushNamed(context, route);
  }

  Future<void> _pickImage() async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.gallery);
    
    if (image != null) {
      final String? newPhoto = await _authService.updatePhoto(image);
      if (newPhoto != null && mounted) {
        _loadProfile();
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Foto profil berhasil diperbarui!')));
      }
    }
  }

  void _showEditProfileDialog() {
    final nameController = TextEditingController(text: _currentUser?.name);
    final nimController = TextEditingController(text: _currentUser?.nim);
    bool isSaving = false;

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setModalState) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          title: Text('Edit Profil Toko', style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Avatar edit
              Stack(
                children: [
                  CircleAvatar(
                    radius: 40,
                    backgroundImage: NetworkImage(
                      _currentUser?.photo != null 
                      ? ApiService.getImageUrl('storage/${_currentUser!.photo}')
                      : 'https://ui-avatars.com/api/?name=${_currentUser?.name ?? "User"}&background=f0f0f0&color=9F1521&bold=true'
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    right: 0,
                    child: GestureDetector(
                      onTap: () {
                        Navigator.pop(context);
                        _pickImage();
                      },
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: const BoxDecoration(color: Color(0xFF9F1521), shape: BoxShape.circle),
                        child: const Icon(Icons.camera_alt, color: Colors.white, size: 14),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),
              TextField(
                controller: nameController,
                decoration: InputDecoration(
                  labelText: 'Nama Toko',
                  labelStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
              const SizedBox(height: 15),
              TextField(
                controller: nimController,
                decoration: InputDecoration(
                  labelText: 'NIM / NPM',
                  labelStyle: GoogleFonts.plusJakartaSans(fontSize: 12),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('Batal', style: TextStyle(color: Colors.grey))),
            ElevatedButton(
              onPressed: isSaving ? null : () async {
                setModalState(() => isSaving = true);
                final success = await _authService.updateProfile({
                  'name': nameController.text,
                  'nim': nimController.text,
                });
                if (!mounted) return;
                if (success) {
                  Navigator.pop(context);
                  _loadProfile();
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Profil berhasil diperbarui!')));
                } else {
                  setModalState(() => isSaving = false);
                }
              },
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF9F1521), foregroundColor: Colors.white),
              child: isSaving ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2)) : const Text('Simpan'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);

    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FA),
      drawer: _currentUser?.id == widget.sellerId 
        ? SellerSidebar(
            user: _currentUser,
            currentRoute: '/seller/profile',
            onNavigate: _handleNavigation,
            onLogout: () async {
              await _authService.logout();
              if (!mounted) return;
              Navigator.pushReplacementNamed(context, '/login');
            },
          )
        : null,
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator(color: Color(0xFF9F1521)))
        : CustomScrollView(
            slivers: [
              _buildSliverAppBar(),
              SliverToBoxAdapter(child: _buildProfileCard()),
              SliverToBoxAdapter(child: _buildNavigationTab()),
              _isProductTab ? _buildProductGrid(formatter) : _buildReviewsList(),
              const SliverToBoxAdapter(child: SizedBox(height: 50)),
            ],
          ),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 180,
      pinned: true,
      backgroundColor: const Color(0xFF9F1521),
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Color(0xFF9F1521), Color(0xFF4A0910)],
            ),
          ),
          child: Opacity(opacity: 0.1, child: Image.network('https://www.transparenttextures.com/patterns/cubes.png', repeat: ImageRepeat.repeat)),
        ),
      ),
      leading: _currentUser?.id == widget.sellerId
          ? IconButton(onPressed: () => Scaffold.of(context).openDrawer(), icon: const Icon(Icons.menu, color: Colors.white))
          : IconButton(onPressed: () => Navigator.pop(context), icon: const Icon(Icons.arrow_back, color: Colors.white)),
      title: Text(_seller?.name ?? 'Profil Penjual', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
    );
  }

  Widget _buildProfileCard() {
    return Container(
      transform: Matrix4.translationValues(0, -50, 0),
      margin: const EdgeInsets.symmetric(horizontal: 20),
      padding: const EdgeInsets.all(25),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.08), blurRadius: 20, offset: const Offset(0, 5))],
      ),
      child: Column(
        children: [
          Row(
            children: [
              // Avatar with verified badge
              Stack(
                children: [
                  Container(
                    width: 80,
                    height: 80,
                    decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 3), boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.1), blurRadius: 10)]),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(40),
                      child: Image.network(
                        _seller?.photo != null ? ApiService.getImageUrl('storage/${_seller!.photo}') : 'https://ui-avatars.com/api/?name=${_seller?.name ?? "User"}&background=f0f0f0&color=9F1521&bold=true',
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),
                  if (_seller?.isVerified ?? false)
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(color: const Color(0xFF28A745), shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2)),
                        child: const Icon(Icons.check, color: Colors.white, size: 10),
                      ),
                    ),
                ],
              ),
              const SizedBox(width: 20),
              // Info
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(_seller?.name ?? '-', style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w900, color: const Color(0xFF1A1A1A))),
                    const SizedBox(height: 4),
                    Row(
                      children: [
                        const Icon(Icons.circle, size: 8, color: Colors.green),
                        const SizedBox(width: 6),
                        Text('Aktif Baru Saja', style: GoogleFonts.plusJakartaSans(fontSize: 11, color: Colors.grey.shade600)),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        SizedBox(
                          height: 32,
                          child: OutlinedButton.icon(
                            onPressed: () {},
                            icon: const Icon(Icons.chat_bubble_outline, size: 14),
                            label: const Text('Chat Penjual'),
                            style: OutlinedButton.styleFrom(
                              foregroundColor: const Color(0xFF9F1521),
                              side: const BorderSide(color: Color(0xFF9F1521)),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                              padding: const EdgeInsets.symmetric(horizontal: 15),
                            ),
                          ),
                        ),
                        if (_currentUser?.id == widget.sellerId) ...[
                          const SizedBox(width: 8),
                          SizedBox(
                            height: 32,
                            child: ElevatedButton.icon(
                              onPressed: () => _showEditProfileDialog(),
                              icon: const Icon(Icons.edit_outlined, size: 14),
                              label: const Text('Edit Profil'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF9F1521),
                                foregroundColor: Colors.white,
                                elevation: 0,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                                padding: const EdgeInsets.symmetric(horizontal: 15),
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 25),
          const Divider(height: 1),
          const SizedBox(height: 20),
          // Stats
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildStatItem(Icons.inventory_2_outlined, 'Produk', _products.length.toString()),
              _buildStatItem(Icons.verified_user_outlined, 'Bergabung', _seller?.createdAt != null ? DateFormat('MMM yyyy').format(_seller!.createdAt!) : '-'),
              _buildStatItem(Icons.star_outline, 'Penilaian', _rating == 0 ? 'Belum ada' : _rating.toStringAsFixed(1)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem(IconData icon, String label, String value) {
    return Column(
      children: [
        Icon(icon, color: const Color(0xFF9F1521), size: 20),
        const SizedBox(height: 4),
        Text(label, style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
        Text(value, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold, color: const Color(0xFF9F1521))),
      ],
    );
  }

  Widget _buildNavigationTab() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: Color(0xFFEEEEEE), width: 2))),
      child: Row(
        children: [
          _buildTabItem('SEMUA PRODUK', _isProductTab, () => setState(() => _isProductTab = true)),
          _buildTabItem('ULASAN SELLER', !_isProductTab, () => setState(() => _isProductTab = false)),
        ],
      ),
    );
  }

  Widget _buildTabItem(String title, bool isActive, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 12),
        decoration: BoxDecoration(border: Border(bottom: BorderSide(color: isActive ? const Color(0xFF9F1521) : Colors.transparent, width: 3))),
        child: Text(title, style: GoogleFonts.plusJakartaSans(fontSize: 11, fontWeight: FontWeight.w900, color: isActive ? const Color(0xFF9F1521) : Colors.grey)),
      ),
    );
  }

  Widget _buildProductGrid(NumberFormat formatter) {
    if (_products.isEmpty) {
      return SliverFillRemaining(
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.storefront_outlined, size: 80, color: Colors.grey.shade200),
              const SizedBox(height: 20),
              Text('Toko Masih Kosong', style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey)),
            ],
          ),
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.all(20),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 15,
          crossAxisSpacing: 15,
          childAspectRatio: 0.7,
        ),
        delegate: SliverChildBuilderDelegate(
          (context, index) => _buildProductCard(_products[index], formatter),
          childCount: _products.length,
        ),
      ),
    );
  }

  Widget _buildProductCard(Product p, NumberFormat formatter) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        border: Border.all(color: const Color(0xFFF0F0F0)),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Image
          Expanded(
            child: Stack(
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.vertical(top: Radius.circular(15)),
                  child: Image.network(p.imageUrl ?? '', width: double.infinity, height: double.infinity, fit: BoxFit.cover, errorBuilder: (c, e, s) => Container(color: Colors.grey.shade100)),
                ),
                Positioned(
                  top: 10,
                  right: 10,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.9), borderRadius: BorderRadius.circular(20)),
                    child: Text(p.category?.name ?? 'Umum', style: GoogleFonts.plusJakartaSans(fontSize: 8, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                  ),
                ),
              ],
            ),
          ),
          // Info
          Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(4)),
                      child: Text(p.condition.toUpperCase(), style: GoogleFonts.plusJakartaSans(fontSize: 8, color: Colors.grey.shade600, fontWeight: FontWeight.bold)),
                    ),
                    const Row(children: [Icon(Icons.star, color: Colors.orange, size: 10), SizedBox(width: 2), Text('5.0', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold))]),
                  ],
                ),
                const SizedBox(height: 8),
                Text(p.name, style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold), maxLines: 2, overflow: TextOverflow.ellipsis),
                const SizedBox(height: 8),
                Text(formatter.format(p.price), style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w900, color: const Color(0xFF9F1521))),
                const SizedBox(height: 12),
                const Divider(height: 1),
                const SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const Icon(Icons.layers_outlined, size: 12, color: Colors.grey),
                        const SizedBox(width: 4),
                        Text('Stok: ${p.stock}', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey)),
                      ],
                    ),
                    Row(
                      children: [
                        _buildSmallAction(Icons.visibility_outlined, Colors.grey.shade100, Colors.grey.shade700),
                        const SizedBox(width: 6),
                        _buildSmallAction(Icons.add_shopping_cart, const Color(0xFF9F1521), Colors.white),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildReviewsList() {
    if (_sellerReviews.isEmpty) {
      return SliverFillRemaining(
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(Icons.rate_review_outlined, size: 80, color: Colors.grey.shade200),
              const SizedBox(height: 20),
              Text('Belum Ada Ulasan', style: GoogleFonts.plusJakartaSans(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.grey)),
            ],
          ),
        ),
      );
    }

    return SliverPadding(
      padding: const EdgeInsets.all(20),
      sliver: SliverList(
        delegate: SliverChildBuilderDelegate(
          (context, index) {
            final review = _sellerReviews[index];
            return Container(
              margin: const EdgeInsets.only(bottom: 15),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15), border: Border.all(color: const Color(0xFFF0F0F0))),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 18,
                        backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${review.user?.name ?? "User"}&background=f0f0f0&color=9F1521&bold=true'),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(review.user?.name ?? 'Pembeli', style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.bold)),
                            Row(
                              children: List.generate(5, (star) => Icon(Icons.star, size: 10, color: star < (review.sellerRating ?? 0) ? Colors.blue : Colors.grey.shade300)),
                            ),
                          ],
                        ),
                      ),
                      Text(DateFormat('dd MMM').format(review.createdAt ?? DateTime.now()), style: GoogleFonts.plusJakartaSans(fontSize: 10, color: Colors.grey)),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Text(review.sellerComment ?? 'Tidak ada komentar.', style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.black87)),
                ],
              ),
            );
          },
          childCount: _sellerReviews.length,
        ),
      ),
    );
  }

  Widget _buildSmallAction(IconData icon, Color bgColor, Color iconColor) {
    return Container(
      width: 26,
      height: 26,
      decoration: BoxDecoration(color: bgColor, shape: BoxShape.circle, border: Border.all(color: Colors.grey.shade200)),
      child: Icon(icon, size: 12, color: iconColor),
    );
  }
}
