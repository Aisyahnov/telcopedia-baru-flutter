import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../services/product_service.dart';
import '../services/auth_service.dart';
import '../models/product.dart';
import '../providers/auth_provider.dart';

import 'wishlist_screen.dart';
import 'order_history_screen.dart';
import 'chat_list_screen.dart';
import 'chat_room_screen.dart';
import 'category_screen.dart';
import 'account_screen.dart';
import '../models/chat.dart';
import '../services/notification_service.dart';
import 'notification_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ProductService _productService = ProductService();
  final AuthService _authService = AuthService();
  final NotificationService _notificationService = NotificationService();
  List<Product> _products = [];
  bool _isLoading = true;
  int _unreadNotifCount = 0;
  final TextEditingController _searchController = TextEditingController();
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadProducts();
    _loadUnreadCount();
  }

  Future<void> _loadUnreadCount() async {
    final count = await _notificationService.getUnreadCount();
    if (mounted) {
      setState(() => _unreadNotifCount = count);
    }
  }

  Future<void> _loadProducts() async {
    final products = await _productService.getProducts(keyword: _searchController.text);
    if (mounted) {
      setState(() {
        _products = products;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> tabs = [
      _buildHomeContent(),
      const CategoryScreen(),
      const OrderHistoryScreen(),
      const WishlistScreen(),
      const AccountScreen(),
    ];

    return Scaffold(
      backgroundColor: const Color(0xFFFCFCFC),
      appBar: _currentIndex == 0 ? _buildAppBar() : null,
      body: tabs[_currentIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) => setState(() => _currentIndex = index),
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFF9F1521),
        unselectedItemColor: Colors.grey,
        selectedLabelStyle: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold),
        unselectedLabelStyle: GoogleFonts.plusJakartaSans(fontSize: 10),
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_outlined), activeIcon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.grid_view_outlined), activeIcon: Icon(Icons.grid_view), label: 'Kategori'),
          BottomNavigationBarItem(icon: Icon(Icons.history_outlined), activeIcon: Icon(Icons.history), label: 'Riwayat'),
          BottomNavigationBarItem(icon: Icon(Icons.favorite_border_outlined), activeIcon: Icon(Icons.favorite), label: 'Wishlist'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), activeIcon: Icon(Icons.person), label: 'Akun'),
        ],
      ),
    );
  }

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: Colors.white,
      elevation: 0.5,
      centerTitle: false,
      automaticallyImplyLeading: false, // No sidebar
      iconTheme: const IconThemeData(color: Colors.black87),
      title: Image.network(
        'http://127.0.0.1:8000/images/logo.png',
        height: 35,
        errorBuilder: (c, e, s) => Text(
          'Telcopedia',
          style: GoogleFonts.plusJakartaSans(
            color: const Color(0xFF9F1521),
            fontWeight: FontWeight.w900,
            fontSize: 20,
          ),
        ),
      ),
      actions: [
        _buildAppBarIcon(Icons.confirmation_number_outlined, '/vouchers'),
        _buildNotificationIcon(),
        _buildAppBarIcon(Icons.chat_outlined, '/chat'),
        _buildCartIcon(),
      ],
      bottom: PreferredSize(
        preferredSize: const Size.fromHeight(60),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
          child: Row(
            children: [
              Expanded(
                child: Container(
                  height: 45,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(100),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari barang atau nama seller...',
                      hintStyle: GoogleFonts.plusJakartaSans(fontSize: 13, color: Colors.grey),
                      prefixIcon: const Icon(Icons.search, size: 20, color: Colors.grey),
                      border: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(vertical: 10),
                    ),
                    onSubmitted: (_) => _loadProducts(),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              GestureDetector(
                onTap: _loadProducts,
                child: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: const BoxDecoration(
                    color: Color(0xFF9F1521),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(Icons.search, color: Colors.white, size: 20),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHomeContent() {
    return RefreshIndicator(
      onRefresh: _loadProducts,
      color: const Color(0xFF9F1521),
      child: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildHero(),
            _buildUSP(),
            _buildProductSection(),
            _buildTestimonials(),
            _buildSellerCTA(),
            const SizedBox(height: 50),
          ],
        ),
      ),
    );
  }

  Widget _buildAppBarIcon(IconData icon, String route) {
    return IconButton(
      icon: Icon(icon, size: 22),
      onPressed: () => Navigator.pushNamed(context, route),
    );
  }

  Widget _buildNotificationIcon() {
    return Stack(
      children: [
        IconButton(
          icon: const Icon(Icons.notifications_outlined, size: 22),
          onPressed: () async {
            await Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationScreen()));
            _loadUnreadCount();
          },
        ),
        if (_unreadNotifCount > 0)
          Positioned(
            right: 8,
            top: 8,
            child: Container(
              padding: const EdgeInsets.all(2),
              decoration: BoxDecoration(
                color: const Color(0xFF9F1521),
                borderRadius: BorderRadius.circular(10),
              ),
              constraints: const BoxConstraints(minWidth: 14, minHeight: 14),
              child: Text(
                _unreadNotifCount > 9 ? '9+' : '$_unreadNotifCount',
                style: GoogleFonts.plusJakartaSans(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildCartIcon() {
    return Stack(
      children: [
        IconButton(
          icon: const Icon(Icons.shopping_cart_outlined, size: 22),
          onPressed: () => Navigator.pushNamed(context, '/cart'),
        ),
        Positioned(
          right: 8,
          top: 8,
          child: IgnorePointer(
            child: Container(
              padding: const EdgeInsets.all(2),
              decoration: BoxDecoration(
                color: const Color(0xFF9F1521),
                borderRadius: BorderRadius.circular(10),
              ),
              constraints: const BoxConstraints(minWidth: 14, minHeight: 14),
              child: Text(
                '3', // Placeholder for cart count
                style: GoogleFonts.plusJakartaSans(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildProfileIcon(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;
    return GestureDetector(
      onTap: () => Scaffold.of(context).openDrawer(),
      child: Padding(
        padding: const EdgeInsets.only(right: 15, left: 5),
        child: CircleAvatar(
          radius: 16,
          backgroundImage: user?.photo != null
              ? NetworkImage('http://10.0.2.2:8000/storage/${user!.photo}')
              : NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(user?.name ?? "")}&background=9F1521&color=fff&bold=true') as ImageProvider,
        ),
      ),
    );
  }

  Widget _buildHero() {
    return Container(
      margin: const EdgeInsets.all(20),
      height: 180,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: Colors.grey.shade100),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 30,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Stack(
        children: [
          Row(
            children: [
              Expanded(
                flex: 6,
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: const Color(0xFF9F1521).withOpacity(0.1),
                          borderRadius: BorderRadius.circular(100),
                        ),
                        child: Text(
                          'TELKOM MARKETPLACE',
                          style: GoogleFonts.plusJakartaSans(
                            color: const Color(0xFF9F1521),
                            fontSize: 8,
                            fontWeight: FontWeight.w800,
                            letterSpacing: 1,
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        'Cari Barang Bekas\nHarga Teman.',
                        style: GoogleFonts.plusJakartaSans(
                          fontWeight: FontWeight.w900,
                          fontSize: 18,
                          height: 1.2,
                        ),
                      ),
                      const SizedBox(height: 5),
                      Text(
                        'Belanja aman dari kawan sendiri di ekosistem Telkom University.',
                        style: GoogleFonts.plusJakartaSans(
                          color: Colors.grey,
                          fontSize: 10,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              Expanded(
                flex: 4,
                child: ClipRRect(
                  borderRadius: const BorderRadius.only(
                    topRight: Radius.circular(24),
                    bottomRight: Radius.circular(24),
                  ),
                  child: Image.network(
                    'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fHVuaXZlcnNpdHklMjBzdHVkZW50c3xlbnwwfHwwfHx8MA%3D%3D',
                    fit: BoxFit.cover,
                    height: double.infinity,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildUSP() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      child: Row(
        children: [
          _buildUSPItem(Icons.shield_outlined, 'Aman', 'User terverifikasi'),
          _buildUSPItem(Icons.local_offer_outlined, 'Murah', 'Harga mahasiswa'),
          _buildUSPItem(Icons.handshake_outlined, 'Mudah', 'COD area kampus'),
        ],
      ),
    );
  }

  Widget _buildUSPItem(IconData icon, String title, String desc) {
    return Expanded(
      child: Column(
        children: [
          Icon(icon, color: const Color(0xFF9F1521), size: 28),
          const SizedBox(height: 8),
          Text(
            title,
            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13),
          ),
          Text(
            desc,
            textAlign: TextAlign.center,
            style: GoogleFonts.plusJakartaSans(color: Colors.grey, fontSize: 9),
          ),
        ],
      ),
    );
  }

  Widget _buildProductSection() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              RichText(
                text: TextSpan(
                  style: GoogleFonts.plusJakartaSans(
                    color: Colors.black,
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                  ),
                  children: const [
                    TextSpan(text: 'Produk '),
                    TextSpan(text: 'Terbaru', style: TextStyle(color: Color(0xFF9F1521))),
                  ],
                ),
              ),
              Text(
                'Lihat Semua',
                style: GoogleFonts.plusJakartaSans(
                  color: const Color(0xFF9F1521),
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          _isLoading
              ? _buildShimmerGrid()
              : GridView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
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
                ),
        ],
      ),
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
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 10,
              offset: const Offset(0, 5),
            ),
          ],
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
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: Colors.grey.shade100,
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Text(
                          p.condition.toUpperCase(),
                          style: GoogleFonts.plusJakartaSans(fontSize: 8, color: Colors.grey.shade600, fontWeight: FontWeight.bold),
                        ),
                      ),
                      Row(
                        children: [
                          const Icon(Icons.star, color: Colors.amber, size: 10),
                          const SizedBox(width: 2),
                          Text('4.8', style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    p.name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.bold, fontSize: 13, height: 1.2),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    formatter.format(p.price),
                    style: GoogleFonts.plusJakartaSans(
                      color: const Color(0xFF9F1521),
                      fontWeight: FontWeight.w900,
                      fontSize: 15,
                    ),
                  ),
                  const SizedBox(height: 10),
                  const Divider(height: 1),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 10,
                        backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(p.seller?.name ?? "S")}&background=F8F9FA&color=9F1521'),
                      ),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          p.seller?.name?.split(' ')[0] ?? 'Seller',
                          style: GoogleFonts.plusJakartaSans(fontSize: 10, fontWeight: FontWeight.bold),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Container(
                        width: 24,
                        height: 24,
                        decoration: const BoxDecoration(
                          color: Color(0xFF9F1521),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.add_shopping_cart, color: Colors.white, size: 12),
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

  Widget _buildTestimonials() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: RichText(
            text: TextSpan(
              style: GoogleFonts.plusJakartaSans(
                color: Colors.black,
                fontSize: 18,
                fontWeight: FontWeight.w800,
              ),
              children: const [
                TextSpan(text: 'Apa Kata '),
                TextSpan(text: 'Mereka?', style: TextStyle(color: Color(0xFF9F1521))),
              ],
            ),
          ),
        ),
        const SizedBox(height: 20),
        SizedBox(
          height: 180,
          child: ListView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 20),
            children: [
              _buildTestiCard('Aisyah Noviani', 'Mahasiswa FIK', 'Awalnya ragu beli barang bekas online, tapi di Telcopedia aman banget.'),
              _buildTestiCard('Siti Amany', 'Mahasiswa FRI', 'Cari buku referensi kuliah jadi lebih gampang dan murah.'),
              _buildTestiCard('Andi Bayu', 'Mahasiswa FIF', 'Jual meja lipat bekas kosan cuma butuh 2 hari langsung laku.'),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTestiCard(String name, String role, String text) {
    return Container(
      width: 260,
      margin: const EdgeInsets.only(right: 15, bottom: 10),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0xFFF0F0F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 18,
                backgroundImage: NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(name)}&background=9F1521&color=fff'),
              ),
              const SizedBox(width: 10),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(name, style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800, fontSize: 13)),
                  Text(role, style: GoogleFonts.plusJakartaSans(color: const Color(0xFF9F1521), fontSize: 10, fontWeight: FontWeight.bold)),
                ],
              ),
            ],
          ),
          const SizedBox(height: 15),
          Text(
            '"$text"',
            style: GoogleFonts.plusJakartaSans(color: Colors.grey.shade600, fontSize: 11, fontStyle: FontStyle.italic),
            maxLines: 3,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  Widget _buildSellerCTA() {
    return Container(
      margin: const EdgeInsets.all(20),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: const Color(0xFF1A1A1A),
        borderRadius: BorderRadius.circular(24),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Punya barang yang tidak terpakai?',
                  style: GoogleFonts.plusJakartaSans(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 15),
                ),
                const SizedBox(height: 5),
                Text(
                  'Ubah barang lama kamu menjadi uang tambahan sekarang!',
                  style: GoogleFonts.plusJakartaSans(color: Colors.white.withOpacity(0.5), fontSize: 11),
                ),
              ],
            ),
          ),
          ElevatedButton(
            onPressed: () {},
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF9F1521),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(100)),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
              elevation: 0,
            ),
            child: Text('Jual Sekarang', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildShimmerGrid() {
    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.65,
        crossAxisSpacing: 15,
        mainAxisSpacing: 15,
      ),
      itemCount: 4,
      itemBuilder: (context, index) => Container(
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
        child: Column(
          children: [
            Expanded(child: Container(color: Colors.grey.shade100)),
            const SizedBox(height: 10),
            Container(height: 10, width: 80, color: Colors.grey.shade100),
            const SizedBox(height: 10),
          ],
        ),
      ),
    );
  }
}
