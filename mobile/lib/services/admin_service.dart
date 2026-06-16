import 'api_service.dart';
import '../models/product.dart';
import '../models/user.dart';
import '../models/penarikan_dana.dart';

class AdminService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> getDashboardStats() async {
    try {
      final response = await _apiService.dio.get('admin/dashboard');
      return response.data['data'];
    } catch (e) {
      return {};
    }
  }

  Future<List<Product>> getAllProducts() async {
    try {
      final response = await _apiService.dio.get('admin/products');
      final List data = response.data['data'];
      return data.map((json) => Product.fromJson(json)).toList();
    } catch (e) {
      return [];
    }
  }

  Future<bool> approveProduct(int id) async {
    try {
      await _apiService.dio.post('admin/products/$id/approve');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> rejectProduct(int id) async {
    try {
      await _apiService.dio.post('admin/products/$id/reject');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteProduct(int id) async {
    try {
      await _apiService.dio.delete('admin/products/$id');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<List<User>> getAllUsers() async {
    try {
      final response = await _apiService.dio.get('admin/users');
      final List data = response.data['data'];
      return data.map((json) => User.fromJson(json)).toList();
    } catch (e) {
      return [];
    }
  }

  Future<List<PenarikanDana>> getAllPenarikanDanas() async {
    try {
      final response = await _apiService.dio.get('admin/penarikan');
      final List data = response.data['data'];
      return data.map((json) => PenarikanDana.fromJson(json)).toList();
    } catch (e) {
      return [];
    }
  }

  Future<bool> approvePenarikanDana(int id) async {
    try {
      await _apiService.dio.post('admin/penarikan/$id/approve');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> rejectPenarikanDana(int id) async {
    try {
      await _apiService.dio.post('admin/penarikan/$id/reject');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteUser(int id) async {
    try {
      await _apiService.dio.delete('admin/users/$id');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<List<dynamic>> getVouchers() async {
    try {
      final response = await _apiService.dio.get('admin/vouchers');
      final data = response.data['data'];
      if (data is Map && data.containsKey('data')) {
        return data['data'];
      }
      return data;
    } catch (e) {
      return [];
    }
  }

  Future<bool> updateVoucher(int id, Map<String, dynamic> data) async {
    try {
      await _apiService.dio.put('admin/vouchers/$id', data: data);
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> storeVoucher(Map<String, dynamic> data) async {
    try {
      await _apiService.dio.post('admin/vouchers', data: data);
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteVoucher(int id) async {
    try {
      await _apiService.dio.delete('admin/vouchers/$id');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<List<dynamic>> getPayments() async {
    try {
      final response = await _apiService.dio.get('admin/payments');
      return response.data['data'];
    } catch (e) {
      return [];
    }
  }
}
