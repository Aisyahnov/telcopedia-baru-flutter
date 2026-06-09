import 'package:dio/dio.dart';
import '../models/product.dart';
import '../models/category.dart';
import '../models/order.dart';
import '../models/product_return.dart';
import '../models/penarikan_dana.dart';
import 'api_service.dart';

class SellerService {
  final Dio _dio = ApiService().dio;

  Future<Map<String, dynamic>?> getDashboardStats() async {
    try {
      final response = await _dio.get('seller/dashboard');
      if (response.statusCode == 200) {
        return response.data['data'];
      }
    } catch (e) {
      // Handle error
    }
    return null;
  }

  Future<List<dynamic>> getSellerReviews() async {
    try {
      final response = await _dio.get('seller/reviews');
      if (response.statusCode == 200) {
        return response.data['data'];
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<List<Product>> getMyProducts() async {
    try {
      final response = await _dio.get('seller/products');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => Product.fromJson(json)).toList();
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<List<Order>> getMyOrders({int page = 1}) async {
    try {
      final response = await _dio.get('seller/orders?page=$page');
      if (response.statusCode == 200) {
        final List data = response.data['data']['data'] ?? response.data['data'];
        return data.map((json) => Order.fromJson(json)).toList();
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<List<dynamic>> getSellerChats() async {
    try {
      final response = await _dio.get('seller/chats');
      if (response.statusCode == 200) {
        return response.data['data'];
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<bool> approvePayment(int orderId) async {
    try {
      final response = await _dio.post('seller/orders/$orderId/approve');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> rejectPayment(int orderId) async {
    try {
      final response = await _dio.post('seller/orders/$orderId/reject');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<List<ProductReturn>> getMyReturns() async {
    try {
      final response = await _dio.get('seller/returns');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => ProductReturn.fromJson(json)).toList();
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<bool> approveReturn(int returnId) async {
    try {
      final response = await _dio.post('seller/returns/$returnId/approve');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> rejectReturn(int returnId) async {
    try {
      final response = await _dio.post('seller/returns/$returnId/reject');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateTracking(int orderId, String trackingNumber) async {
    try {
      final response = await _dio.put(
        'seller/orders/$orderId/tracking',
        data: {'tracking_number': trackingNumber},
      );
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<List<PenarikanDana>> getPenarikanDanas() async {
    try {
      final response = await _dio.get('seller/penarikan');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => PenarikanDana.fromJson(json)).toList();
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<bool> requestPenarikanDana(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post(
        'seller/penarikan',
        data: data,
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<List<Category>> getCategories() async {
    try {
      final response = await _dio.get('categories');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => Category.fromJson(json)).toList();
      }
    } catch (e) {
      // Handle error
    }
    return [];
  }

  Future<bool> storeProduct(Map<String, dynamic> data) async {
    try {
      final response = await _dio.post(
        'seller/products',
        data: data,
      );
      return response.statusCode == 201 || response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateProduct(int id, Map<String, dynamic> data) async {
    try {
      final response = await _dio.put(
        'seller/products/$id',
        data: data,
      );
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteProduct(int id) async {
    try {
      final response = await _dio.delete('seller/products/$id');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
