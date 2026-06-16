class Voucher {
  final int id;
  final String code;
  final double discountAmount;
  final double minSpend;
  final DateTime? validUntil;

  Voucher({
    required this.id,
    required this.code,
    required this.discountAmount,
    required this.minSpend,
    this.validUntil,
  });

  factory Voucher.fromJson(Map<String, dynamic> json) {
    return Voucher(
      id: json['id'],
      code: json['code'],
      discountAmount: _toDouble(json['discount_amount']),
      minSpend: _toDouble(json['min_spend']),
      validUntil: json['valid_until'] != null ? DateTime.parse(json['valid_until']) : null,
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
