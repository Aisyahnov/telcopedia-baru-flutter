import 'product.dart';
import '../services/api_service.dart';
import 'user.dart';

class Order {
  final int id;
  final int userId;
  final String shippingAddress;
  final String paymentMethod;
  final double subtotalAmount;
  final double discountAmount;
  final double adminFee;
  final double totalAmount;
  final String status;
  final String? paymentProof;
  final String? trackingNumber;
  final List<OrderItem> items;
  final DateTime createdAt;
  final User? user;
  final List<dynamic>? reviews;
  final List<dynamic>? returns;

  Order({
    required this.id,
    required this.userId,
    required this.shippingAddress,
    required this.paymentMethod,
    required this.subtotalAmount,
    required this.discountAmount,
    required this.adminFee,
    required this.totalAmount,
    required this.status,
    this.paymentProof,
    this.trackingNumber,
    required this.items,
    required this.createdAt,
    this.user,
    this.reviews,
    this.returns,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      userId: json['user_id'],
      shippingAddress: json['shipping_address'] ?? '',
      paymentMethod: json['payment_method'] ?? 'transfer',
      subtotalAmount: double.parse(json['subtotal_amount'].toString()),
      discountAmount: double.parse(json['discount_amount'].toString()),
      adminFee: double.parse(json['admin_fee'].toString()),
      totalAmount: double.parse(json['total_amount'].toString()),
      status: json['status'] ?? 'pending_payment',
      paymentProof: _formatImageUrl(json['payment_proof']),
      trackingNumber: json['tracking_number'],
      items: json['items'] != null 
        ? (json['items'] as List).map((i) => OrderItem.fromJson(i)).toList()
        : [],
      createdAt: DateTime.parse(json['created_at']),
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      reviews: json['reviews'] as List<dynamic>?,
      returns: json['returns'] as List<dynamic>?,
    );
  }

  static String? _formatImageUrl(String? url) {
    if (url == null || url.isEmpty) return null;
    if (url.startsWith('http')) return url;
    return ApiService.getImageUrl('files/proxy?path=$url');
  }
}

class OrderItem {
  final int id;
  final int orderId;
  final int productId;
  final int quantity;
  final double price;
  final Product? product;

  OrderItem({
    required this.id,
    required this.orderId,
    required this.productId,
    required this.quantity,
    required this.price,
    this.product,
  });

  factory OrderItem.fromJson(Map<String, dynamic> json) {
    return OrderItem(
      id: json['id'],
      orderId: json['order_id'],
      productId: json['product_id'],
      quantity: json['quantity'],
      price: double.parse(json['price'].toString()),
      product: json['product'] != null ? Product.fromJson(json['product']) : null,
    );
  }
}
