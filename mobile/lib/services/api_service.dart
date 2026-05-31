import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Gunakan 10.0.2.2 untuk Android emulator, 127.0.0.1 untuk iOS simulator / desktop.
  // Jika menggunakan perangkat fisik, ganti dengan IP lokal komputer Anda.
  static String get baseUrl {
    if (kIsWeb) return "http://127.0.0.1:8000/api/";
    if (defaultTargetPlatform == TargetPlatform.android) {
      return "http://10.0.2.2:8000/api/";
    }
    return "http://127.0.0.1:8000/api/";
  }

  static String getImageUrl(String path) {
    // Menghapus trailing slash dari baseUrl dan awalan slash dari path
    String base = baseUrl.replaceAll('/api/', '');
    if (path.startsWith('/')) {
      path = path.substring(1);
    }
    return "$base/$path";
  }

  final Dio _dio = Dio(
    BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ),
  );

  ApiService() {
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final prefs = await SharedPreferences.getInstance();
          final token = prefs.getString('token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) {
          if (e.response?.statusCode == 401) {
            // Handle unauthorized (token expired, etc)
          }
          return handler.next(e);
        },
      ),
    );
  }

  Dio get dio => _dio;
}
