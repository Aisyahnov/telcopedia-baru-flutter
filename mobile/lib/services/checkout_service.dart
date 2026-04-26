import 'package:dio/dio.dart';
import 'package:image_picker/image_picker.dart';
import 'package:flutter/foundation.dart';
import 'api_service.dart';
import '../models/order.dart';

class CheckoutService {
  final ApiService _apiService = ApiService();

  Future<Order?> processCheckout({
    required String shippingAddress,
    required String paymentMethod,
    int? productId,
    String? cartItemIds,
    String? voucherCode,
  }) async {
    try {
      final response = await _apiService.dio.post('checkout/save', data: {
        'shipping_address': shippingAddress,
        'payment_method': paymentMethod,
        if (productId != null) 'product_id': productId,
        if (cartItemIds != null) 'cart_item_ids': cartItemIds,
        if (voucherCode != null) 'voucher_code': voucherCode,
      });
      
      if (response.statusCode == 201) {
        return Order.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<Order?> uploadPaymentProof(int orderId, XFile image) async {
    try {
      String fileName = image.name;
      FormData formData;

      if (kIsWeb) {
        formData = FormData.fromMap({
          "payment_proof": MultipartFile.fromBytes(
            await image.readAsBytes(),
            filename: fileName,
          ),
        });
      } else {
        formData = FormData.fromMap({
          "payment_proof": await MultipartFile.fromFile(
            image.path,
            filename: fileName,
          ),
        });
      }

      final response = await _apiService.dio.post(
        'checkout/upload/$orderId',
        data: formData,
      );

      if (response.statusCode == 200) {
        return Order.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}
