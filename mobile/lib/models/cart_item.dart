import 'product.dart';

class CartItem {
  final int id;
  final int cartId;
  final int productId;
  final int quantity;
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

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'cart_id': cartId,
      'product_id': productId,
      'quantity': quantity,
      'product': product?.toJson(),
    };
  }
}
