import 'user.dart';

class PenarikanDana {
  final int id;
  final int userId;
  final double amount;
  final String bankName;
  final String accountNumber;
  final String accountName;
  final String status;
  final User? user;
  final DateTime createdAt;

  PenarikanDana({
    required this.id,
    required this.userId,
    required this.amount,
    required this.bankName,
    required this.accountNumber,
    required this.accountName,
    required this.status,
    this.user,
    required this.createdAt,
  });

  factory PenarikanDana.fromJson(Map<String, dynamic> json) {
    return PenarikanDana(
      id: json['id'],
      userId: json['user_id'],
      amount: _toDouble(json['amount']),
      bankName: json['bank_name'] ?? '',
      accountNumber: json['account_number'] ?? '',
      accountName: json['account_name'] ?? '',
      status: json['status'] ?? 'pending',
      user: json['user'] != null ? User.fromJson(json['user']) : null,
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}
