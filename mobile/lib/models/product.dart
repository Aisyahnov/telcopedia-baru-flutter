import 'user.dart';
import 'category.dart';
import 'product_image.dart';
import 'review.dart';

class Product {
  final int id;
  final int sellerId;
  final int? categoryId;
  final String name;
  final String description;
  final double price;
  final int stock;
  final String? imageUrl;
  final String status;
  final String condition; // Tambahan field condition
  final Category? category;
  final User? seller;
  final List<ProductImage> images;
  final List<Review> reviews;

  Product({
    required this.id,
    required this.sellerId,
    this.categoryId,
    required this.name,
    required this.description,
    required this.price,
    required this.stock,
    this.imageUrl,
    required this.status,
    this.condition = 'Very Good', // Default value
    this.category,
    this.seller,
    this.images = const [],
    this.reviews = const [],
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      sellerId: json['seller_id'],
      categoryId: json['category_id'],
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      imageUrl: _formatImageUrl(json['image_url']),
      price: _toDouble(json['price']),
      stock: json['stock'] ?? 0,
      status: json['status'] ?? 'active',
      condition: json['condition'] ?? 'Very Good',
      category: json['category'] != null ? Category.fromJson(json['category']) : null,
      seller: json['seller'] != null ? User.fromJson(json['seller']) : null,
      images: json['images'] != null 
          ? (json['images'] as List).map((i) => ProductImage.fromJson(i)).toList()
          : [],
      reviews: json['reviews'] != null 
          ? (json['reviews'] as List).map((i) => Review.fromJson(i)).toList()
          : [],
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  static String _formatImageUrl(String? url) {
    if (url == null || url.isEmpty) return 'https://via.placeholder.com/150';
    if (url.startsWith('http')) return url;
    return 'http://127.0.0.1:8000/api/files/proxy?path=$url';
  }
}
