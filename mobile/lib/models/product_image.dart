import '../services/api_service.dart';

class ProductImage {
  final int id;
  final int productId;
  final String imageUrl;

  ProductImage({
    required this.id,
    required this.productId,
    required this.imageUrl,
  });

  factory ProductImage.fromJson(Map<String, dynamic> json) {
    return ProductImage(
      id: json['id'],
      productId: json['product_id'],
      imageUrl: _formatImageUrl(json['image_url']),
    );
  }

  static String _formatImageUrl(String? url) {
    if (url == null || url.isEmpty) return '';
    if (url.startsWith('http')) return url;
    return ApiService.getImageUrl('storage/$url');
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'image_url': imageUrl,
    };
  }
}
