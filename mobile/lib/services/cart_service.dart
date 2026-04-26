import 'api_service.dart';
import '../models/cart.dart';

class CartService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>?> getCart() async {
    try {
      final response = await _apiService.dio.get('cart');
      if (response.statusCode == 200) {
        return {
          'cart': Cart.fromJson(response.data['data']),
          'total': response.data['total'],
        };
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<bool> addToCart(int productId, int quantity) async {
    try {
      final response = await _apiService.dio.post('cart/add', data: {
        'product_id': productId,
        'quantity': quantity,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateQuantity(int itemId, int quantity) async {
    try {
      final response = await _apiService.dio.put('cart/update', data: {
        'item_id': itemId,
        'quantity': quantity,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> removeItem(int itemId) async {
    try {
      final response = await _apiService.dio.delete('cart/remove/$itemId');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<Map<String, dynamic>?> applyVoucher(String code) async {
    try {
      final response = await _apiService.dio.post('cart/voucher', data: {'code': code});
      if (response.statusCode == 200) {
        return {
          'success': true,
          'discount': response.data['discount_amount'],
          'message': response.data['message'],
        };
      }
      return {'success': false, 'message': 'Voucher tidak valid'};
    } catch (e) {
      return {'success': false, 'message': 'Terjadi kesalahan saat menerapkan voucher'};
    }
  }
}
