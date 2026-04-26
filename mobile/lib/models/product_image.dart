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
    return 'http://127.0.0.1:8000/api/files/proxy?path=$url';
  }
}
