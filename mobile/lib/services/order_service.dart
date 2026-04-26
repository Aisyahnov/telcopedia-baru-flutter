import 'package:dio/dio.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/order.dart';

class OrderService {
  final ApiService _apiService = ApiService();

  Future<List<Order>> getBuyerOrders() async {
    try {
      final response = await _apiService.dio.get('orders');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => Order.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<bool> completeOrder(int orderId) async {
    try {
      final response = await _apiService.dio.post('orders/$orderId/complete');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> storeReview({
    required int orderId,
    required int productId,
    required int rating,
    String? comment,
    XFile? media,
  }) async {
    try {
      MultipartFile? multipartFile;
      if (media != null) {
        if (kIsWeb) {
          multipartFile = MultipartFile.fromBytes(
            await media.readAsBytes(),
            filename: media.name,
          );
        } else {
          multipartFile = await MultipartFile.fromFile(
            media.path,
            filename: media.name,
          );
        }
      }

      FormData formData = FormData.fromMap({
        'order_id': orderId,
        'product_id': productId,
        'rating': rating,
        'comment': comment,
        if (multipartFile != null) 'media': multipartFile,
      });

      final response = await _apiService.dio.post('reviews', data: formData);
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> storeReturn({
    required int orderId,
    required int productId,
    required String reason,
    XFile? media,
  }) async {
    try {
      MultipartFile? multipartFile;
      if (media != null) {
        if (kIsWeb) {
          multipartFile = MultipartFile.fromBytes(
            await media.readAsBytes(),
            filename: media.name,
          );
        } else {
          multipartFile = await MultipartFile.fromFile(
            media.path,
            filename: media.name,
          );
        }
      }

      FormData formData = FormData.fromMap({
        'order_id': orderId,
        'product_id': productId,
        'reason': reason,
        if (multipartFile != null) 'media': multipartFile,
      });

      final response = await _apiService.dio.post('returns', data: formData);
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> uploadPaymentProof(int orderId, XFile proof) async {
    try {
      MultipartFile multipartFile;
      if (kIsWeb) {
        multipartFile = MultipartFile.fromBytes(
          await proof.readAsBytes(),
          filename: proof.name,
        );
      } else {
        multipartFile = await MultipartFile.fromFile(
          proof.path,
          filename: proof.name,
        );
      }

      FormData formData = FormData.fromMap({
        'payment_proof': multipartFile,
      });

      final response = await _apiService.dio.post('checkout/upload/$orderId', data: formData);
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
