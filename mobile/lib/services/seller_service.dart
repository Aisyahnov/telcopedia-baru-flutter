import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/product.dart';
import '../models/category.dart';
import '../models/order.dart';
import '../models/product_return.dart';
import '../models/withdrawal.dart';

class SellerService {
  final String baseUrl = 'http://127.0.0.1:8000/api';

  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  Future<List<Product>> getMyProducts() async {
    final token = await _getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/seller/products'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List data = json.decode(response.body)['data'];
      return data.map((json) => Product.fromJson(json)).toList();
    }
    return [];
  }

  Future<List<Order>> getMyOrders() async {
    final token = await _getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/seller/orders'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List data = json.decode(response.body)['data'];
      return data.map((json) => Order.fromJson(json)).toList();
    }
    return [];
  }

  Future<bool> approvePayment(int orderId) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/orders/$orderId/approve'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 200;
  }

  Future<bool> rejectPayment(int orderId) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/orders/$orderId/reject'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 200;
  }

  Future<List<ProductReturn>> getMyReturns() async {
    final token = await _getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/seller/returns'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List data = json.decode(response.body)['data'];
      return data.map((json) => ProductReturn.fromJson(json)).toList();
    }
    return [];
  }

  Future<bool> approveReturn(int returnId) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/returns/$returnId/approve'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 200;
  }

  Future<bool> rejectReturn(int returnId) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/returns/$returnId/reject'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 200;
  }

  Future<bool> updateTracking(int orderId, String trackingNumber) async {
    final token = await _getToken();
    final response = await http.put(
      Uri.parse('$baseUrl/seller/orders/$orderId/tracking'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({'tracking_number': trackingNumber}),
    );
    return response.statusCode == 200;
  }

  Future<List<Withdrawal>> getWithdrawals() async {
    final token = await _getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/seller/withdrawals'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List data = json.decode(response.body)['data'];
      return data.map((json) => Withdrawal.fromJson(json)).toList();
    }
    return [];
  }

  Future<bool> requestWithdrawal(Map<String, dynamic> data) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/withdrawals'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode(data),
    );
    return response.statusCode == 201 || response.statusCode == 200;
  }

  Future<List<Category>> getCategories() async {
    final response = await http.get(Uri.parse('$baseUrl/categories'));
    if (response.statusCode == 200) {
      final List data = json.decode(response.body);
      return data.map((json) => Category.fromJson(json)).toList();
    }
    return [];
  }

  Future<bool> storeProduct(Map<String, dynamic> data) async {
    final token = await _getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/seller/products'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode(data),
    );
    return response.statusCode == 201 || response.statusCode == 200;
  }

  Future<bool> updateProduct(int id, Map<String, dynamic> data) async {
    final token = await _getToken();
    final response = await http.put(
      Uri.parse('$baseUrl/seller/products/$id'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode(data),
    );
    return response.statusCode == 200;
  }

  Future<bool> deleteProduct(int id) async {
    final token = await _getToken();
    final response = await http.delete(
      Uri.parse('$baseUrl/seller/products/$id'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 200;
  }
}
