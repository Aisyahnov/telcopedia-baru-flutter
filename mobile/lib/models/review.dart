import 'user.dart';

class Review {
  final int id;
  final int productId;
  final int userId;
  final int rating;
  final String? comment;
  final DateTime? createdAt;
  final User? user;

  Review({
    required this.id,
    required this.productId,
    required this.userId,
    required this.rating,
    this.comment,
    this.createdAt,
    this.user,
  });

  factory Review.fromJson(Map<String, dynamic> json) {
    return Review(
      id: json['id'],
      productId: json['product_id'],
      userId: json['user_id'],
      rating: json['rating'] ?? 0,
      comment: json['comment'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      user: json['user'] != null ? User.fromJson(json['user']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'product_id': productId,
      'user_id': userId,
      'rating': rating,
      'comment': comment,
      'created_at': createdAt?.toIso8601String(),
      'user': user?.toJson(),
    };
  }
}
