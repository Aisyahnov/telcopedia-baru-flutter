import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/notification.dart';

class NotificationService {
  final ApiService _apiService = ApiService();

  Future<List<SystemNotification>> getNotifications() async {
    try {
      final response = await _apiService.dio.get('notifications');
      if (response.statusCode == 200) {
        final List data = response.data['data']['data'];
        return data.map((json) => SystemNotification.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching notifications: $e');
      return [];
    }
  }

  Future<int> getUnreadCount() async {
    try {
      final response = await _apiService.dio.get('notifications/count');
      if (response.statusCode == 200) {
        return response.data['count'] ?? 0;
      }
      return 0;
    } catch (e) {
      return 0;
    }
  }

  Future<bool> markAsRead(String id) async {
    try {
      final response = await _apiService.dio.post('notifications/$id/read');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> markAllAsRead() async {
    try {
      final response = await _apiService.dio.post('notifications/read-all');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
