import 'product.dart';

class Cart {
  final int id;
  final int userId;
  final int? voucherId;
  final List<CartItem> items;
  final dynamic voucher; // Voucher model could be added later

  Cart({
    required this.id,
    required this.userId,
    this.voucherId,
    required this.items,
    this.voucher,
  });

  factory Cart.fromJson(Map<String, dynamic> json) {
    return Cart(
      id: json['id'],
      userId: json['user_id'],
      voucherId: json['voucher_id'],
      items: json['items'] != null 
        ? (json['items'] as List).map((i) => CartItem.fromJson(i)).toList()
        : [],
      voucher: json['voucher'],
    );
  }
}

class CartItem {
  final int id;
  final int cartId;
  final int productId;
  int quantity;
  final Product? product;

  CartItem({
    required this.id,
    required this.cartId,
    required this.productId,
    required this.quantity,
    this.product,
  });

  factory CartItem.fromJson(Map<String, dynamic> json) {
    return CartItem(
      id: json['id'],
      cartId: json['cart_id'],
      productId: json['product_id'],
      quantity: json['quantity'],
      product: json['product'] != null ? Product.fromJson(json['product']) : null,
    );
  }
}
