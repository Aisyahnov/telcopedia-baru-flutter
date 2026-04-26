import 'product.dart';
import 'user.dart';

class ProductReturn {
  final int id;
  final int orderId;
  final int productId;
  final int userId;
  final String reason;
  final String? media;
  final String status;
  final Product? product;
  final User? user;
  final DateTime createdAt;
  final DateTime updatedAt;

  ProductReturn({
    required this.id,
    required this.orderId,
    required this.productId,
    required this.userId,
    required this.reason,
    this.media,
    required this.status,
    this.product,
    this.user,
    required this.createdAt,
    required this.updatedAt,
  });

  factory ProductReturn.fromJson(Map<String, dynamic> json) {
    return ProductReturn(
      id: json['id'],
      orderId: json['order_id'],
      productId: json['product_id'],
      userId: json['user_id'],
      reason: json['reason'] ?? '',
      media: _formatImageUrl(json['media']),
      status: json['status'] ?? 'pending',
      product: json['product'] != null ? Product.fromJson(json['product']) : null,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  static String? _formatImageUrl(String? url) {
    if (url == null || url.isEmpty) return null;
    if (url.startsWith('http')) return url;
    return 'http://127.0.0.1:8000/api/files/proxy?path=$url';
  }
}
