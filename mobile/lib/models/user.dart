class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final String? nim;
  final String? photo;
  final double balance;
  final int penaltyPoints;
  final bool isBannedFromPosting;
  final bool isVerified;
  final String? phone;
  final String? address;
  final String? ktm;
  final DateTime? createdAt;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.nim,
    this.photo,
    this.balance = 0.0,
    this.penaltyPoints = 0,
    this.isBannedFromPosting = false,
    this.isVerified = false,
    this.phone,
    this.address,
    this.ktm,
    this.createdAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'buyer',
      nim: json['nim'],
      photo: json['photo'],
      balance: _toDouble(json['balance']),
      penaltyPoints: json['penalty_points'] ?? 0,
      isBannedFromPosting: json['is_banned_from_posting'] == 1 || json['is_banned_from_posting'] == true,
      isVerified: json['is_verified'] == 1 || json['is_verified'] == true,
      phone: json['phone'],
      address: json['address'],
      ktm: json['ktm'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'nim': nim,
      'photo': photo,
      'balance': balance,
      'penalty_points': penaltyPoints,
      'is_banned_from_posting': isBannedFromPosting,
      'is_verified': isVerified,
      'phone': phone,
      'address': address,
      'ktm': ktm,
      'created_at': createdAt?.toIso8601String(),
    };
  }
}
