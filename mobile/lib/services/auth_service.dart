import 'package:dio/dio.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import 'api_service.dart';

class AuthService {
  final ApiService _apiService = ApiService();

  Future<Map<String, dynamic>> login(String email, String nim, String password) async {
    try {
      final response = await _apiService.dio.post('login', data: {
        'email': email,
        'nim': nim,
        'password': password,
      });

      if (response.statusCode == 200) {
        final data = response.data['data'];
        final token = response.data['token'];

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', token);
        
        return {
          'success': true,
          'user': User.fromJson(data),
          'token': token,
        };
      }
      return {'success': false, 'message': 'Unknown error'};
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Login failed',
      };
    }
  }

  Future<Map<String, dynamic>> register({
    String? nim,
    required String name,
    required String email,
    required String password,
    required String phone,
    String role = 'buyer',
  }) async {
    try {
      final response = await _apiService.dio.post('register', data: {
        'nim': nim,
        'name': name,
        'email': email,
        'password': password,
        'phone': phone,
        'role': role,
      });

      if (response.statusCode == 201) {
        final data = response.data['data'];
        final token = response.data['token'];

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', token);

        return {
          'success': true,
          'user': User.fromJson(data),
          'token': token,
        };
      }
      return {'success': false, 'message': 'Unknown error'};
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Registration failed',
      };
    }
  }

  Future<void> logout() async {
    try {
      await _apiService.dio.post('logout');
    } catch (_) {}
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
  }

  Future<User?> getCurrentUser() async {
    try {
      final response = await _apiService.dio.get('user');
      if (response.statusCode == 200) {
        return User.fromJson(response.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<bool> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _apiService.dio.put('user/profile', data: data);
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updatePassword(String currentPassword, String newPassword) async {
    try {
      final response = await _apiService.dio.put('user/password', data: {
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': newPassword,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<String?> updatePhoto(XFile file) async {
    try {
      MultipartFile multipartFile;
      if (kIsWeb) {
        multipartFile = MultipartFile.fromBytes(
          await file.readAsBytes(),
          filename: file.name,
        );
      } else {
        multipartFile = await MultipartFile.fromFile(
          file.path,
          filename: file.name,
        );
      }

      FormData formData = FormData.fromMap({
        "photo": multipartFile,
      });
      final response = await _apiService.dio.post('user/photo', data: formData);
      if (response.statusCode == 200) {
        return response.data['photo'];
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<String?> updateKtm(XFile file) async {
    try {
      MultipartFile multipartFile;
      if (kIsWeb) {
        multipartFile = MultipartFile.fromBytes(
          await file.readAsBytes(),
          filename: file.name,
        );
      } else {
        multipartFile = await MultipartFile.fromFile(
          file.path,
          filename: file.name,
        );
      }

      FormData formData = FormData.fromMap({
        "ktm": multipartFile,
      });
      final response = await _apiService.dio.post('user/ktm', data: formData);
      if (response.statusCode == 200) {
        return response.data['ktm'];
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}
