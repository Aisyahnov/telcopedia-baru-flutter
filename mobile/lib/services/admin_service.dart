import 'api_service.dart';
import '../models/product.dart';
import '../models/user.dart';
import '../models/withdrawal.dart';

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

  Future<List<Withdrawal>> getAllWithdrawals() async {
    try {
      final response = await _apiService.dio.get('admin/withdrawals');
      final List data = response.data['data'];
      return data.map((json) => Withdrawal.fromJson(json)).toList();
    } catch (e) {
      return [];
    }
  }

  Future<bool> approveWithdrawal(int id) async {
    try {
      await _apiService.dio.post('admin/withdrawals/$id/approve');
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<bool> rejectWithdrawal(int id) async {
    try {
      await _apiService.dio.post('admin/withdrawals/$id/reject');
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
      return response.data['data'];
    } catch (e) {
      return [];
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
