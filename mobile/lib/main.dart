import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'providers/auth_provider.dart';
import 'screens/login_screen.dart';
import 'screens/home_screen.dart';
import 'screens/admin/dashboard_screen.dart';
import 'screens/seller/dashboard_screen.dart';
import 'screens/seller/products_screen.dart';
import 'screens/seller/orders_screen.dart';
import 'screens/seller/returns_screen.dart';
import 'screens/seller/penarikan_screen.dart';
import 'screens/wishlist_screen.dart';
import 'screens/voucher_screen.dart';
import 'screens/account_screen.dart';
import 'screens/checkout_screen.dart';
import 'screens/payment_screen.dart';
import 'screens/cart_screen.dart';
import 'screens/chat_list_screen.dart';
import 'screens/chat_room_screen.dart';
import 'models/order.dart' as model;
import 'models/cart.dart';
import 'screens/seller/profile_screen.dart';
import 'screens/seller/settings_screen.dart';
import 'screens/seller/add_product_screen.dart';
import 'screens/seller/edit_product_screen.dart';
import 'screens/product_detail_screen.dart';
import 'models/product.dart';
import 'models/chat.dart';
import 'screens/static_page_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()..checkAuth()),
      ],
      child: MaterialApp(
        title: 'Telcopedia',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          useMaterial3: true,
          colorScheme: ColorScheme.fromSeed(
            seedColor: const Color(0xFF9F1521),
            primary: const Color(0xFF9F1521),
            secondary: const Color(0xFF1A1A1A),
            surface: Colors.white,
          ),
          scaffoldBackgroundColor: const Color(0xFFF8F9FA),
          appBarTheme: const AppBarTheme(
            backgroundColor: Color(0xFF1A1A1A),
            foregroundColor: Colors.white,
            elevation: 0,
            centerTitle: false,
            iconTheme: IconThemeData(color: Color(0xFF9F1521)),
            titleTextStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
          ),
          cardTheme: CardThemeData(
            elevation: 2,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
            color: Colors.white,
          ),
          textTheme: GoogleFonts.plusJakartaSansTextTheme(),
        ),
        home: const AuthWrapper(),
        routes: {
          '/login': (context) => const LoginScreen(),
          '/admin/dashboard': (context) => const AdminDashboardScreen(),
          '/seller/dashboard': (context) => const SellerDashboardScreen(),
          '/seller/products': (context) => const SellerProductsScreen(),
          '/seller/products/create': (context) => const SellerAddProductScreen(),
          '/seller/orders': (context) => const SellerOrdersScreen(),
          '/seller/returns': (context) => const SellerReturnsScreen(),
          '/seller/chats': (context) => const ChatListScreen(),
          '/seller/penarikan': (context) => const SellerPenarikanDanaScreen(),
          '/home': (context) => const HomeScreen(),
          '/cart': (context) => const CartScreen(),
          '/chat': (context) => const ChatListScreen(),
          '/favorites': (context) => const Scaffold(body: Center(child: Text('Halaman Favorit'))),
          '/vouchers': (context) => const VoucherScreen(),
          '/profile': (context) => const SellerSettingsScreen(),
          '/orders': (context) => const Scaffold(body: Center(child: Text('Halaman Riwayat Belanja'))),
          '/about': (context) => const StaticPageScreen(
            title: 'Tentang Telcopedia',
            content: 'Telcopedia adalah platform jual-beli eksklusif bagi mahasiswa Telkom University. Kami memfasilitasi transaksi barang preloved yang aman, mudah, dan terpercaya di lingkungan kampus.',
          ),
          '/contact': (context) => const StaticPageScreen(
            title: 'Hubungi Kami',
            content: 'Jika Anda memiliki pertanyaan atau kendala, silakan hubungi tim dukungan kami melalui WhatsApp: +62 812-3456-7890 atau Email: cs@telcopedia.id.',
          ),
          '/privacy': (context) => const StaticPageScreen(
            title: 'Kebijakan Privasi',
            content: 'Kami menjaga kerahasiaan data pribadi Anda. Data NIM dan informasi profil hanya digunakan untuk proses verifikasi keanggotaan dalam ekosistem kampus Telkom University.',
          ),
          '/terms': (context) => const StaticPageScreen(
            title: 'Syarat & Ketentuan',
            content: 'Pengguna wajib menggunakan identitas asli. Transaksi COD disarankan dilakukan di area publik kampus untuk keamanan bersama.',
          ),
        },
        onGenerateRoute: (settings) {
          if (settings.name == '/product-detail') {
            final productId = settings.arguments as int;
            return MaterialPageRoute(builder: (context) => ProductDetailScreen(productId: productId));
          }
          if (settings.name == '/seller/products/edit') {
            final product = settings.arguments as Product;
            return MaterialPageRoute(builder: (context) => SellerEditProductScreen(product: product));
          }
          if (settings.name == '/chat/room') {
            final chat = settings.arguments as Chat;
            return MaterialPageRoute(builder: (context) => ChatRoomScreen(chat: chat));
          }
          if (settings.name == '/seller/profile') {
            final sellerId = settings.arguments is int ? settings.arguments as int : null;
            if (sellerId == null) {
              return MaterialPageRoute(
                builder: (context) => const Scaffold(
                  body: Center(child: Text('Error: Seller ID is missing')),
                ),
              );
            }
            return MaterialPageRoute(builder: (context) => SellerProfileScreen(sellerId: sellerId));
          }
          if (settings.name == '/seller/settings') {
            return MaterialPageRoute(builder: (context) => const SellerSettingsScreen());
          }
          if (settings.name == '/checkout') {
            final args = settings.arguments as Map<String, dynamic>;
            return MaterialPageRoute(
              builder: (context) => CheckoutScreen(
                items: args['items'] as List<CartItem>,
                cartItemIds: args['cartItemIds'] as String?,
                productId: args['productId'] as int?,
                appliedVoucher: args['appliedVoucher'] as String?,
                voucherDiscount: (args['voucherDiscount'] ?? 0.0) as double,
              ),
            );
          }
          if (settings.name == '/payment') {
            final order = settings.arguments as model.Order;
            return MaterialPageRoute(builder: (context) => PaymentScreen(order: order));
          }
          return null;
        },
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    if (authProvider.isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator(color: Color(0xFF9F1521))),
      );
    }

    if (authProvider.isAuthenticated) {
      if (authProvider.isAdmin) return const AdminDashboardScreen();
      if (authProvider.isSeller) return const SellerDashboardScreen();
      return const HomeScreen();
    }

    return const LoginScreen();
  }
}
