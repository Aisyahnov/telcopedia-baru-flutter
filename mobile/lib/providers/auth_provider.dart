import 'package:flutter/material.dart';
import '../models/user.dart';
import '../services/auth_service.dart';

class AuthProvider with ChangeNotifier {
  final AuthService _authService = AuthService();
  User? _user;
  bool _isLoading = false;
  String? _error;

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isAuthenticated => _user != null;
  bool get isAdmin => _user?.role == 'admin';
  bool get isSeller => _user?.role == 'seller';

  Future<bool> login(String email, String nim, String password) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    final result = await _authService.login(email, nim, password);
    
    _isLoading = false;
    if (result['success']) {
      _user = result['user'];
      notifyListeners();
      return true;
    } else {
      _error = result['message'];
      notifyListeners();
      return false;
    }
  }

  Future<bool> register({
    String? nim,
    required String name,
    required String email,
    required String password,
    String role = 'buyer',
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    final result = await _authService.register(
      nim: nim,
      name: name,
      email: email,
      password: password,
      role: role,
    );

    _isLoading = false;
    if (result['success']) {
      _user = result['user'];
      notifyListeners();
      return true;
    } else {
      _error = result['message'];
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await _authService.logout();
    _user = null;
    notifyListeners();
  }

  Future<void> checkAuth() async {
    _isLoading = true;
    notifyListeners();
    
    _user = await _authService.getCurrentUser();
    
    _isLoading = false;
    notifyListeners();
  }
}
