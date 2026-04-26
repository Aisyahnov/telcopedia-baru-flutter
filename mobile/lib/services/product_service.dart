import '../models/product.dart';
import '../models/category.dart';
import 'api_service.dart';

class ProductService {
  final ApiService _apiService = ApiService();

  Future<List<Product>> getProducts({String? keyword, int? categoryId}) async {
    try {
      final response = await _apiService.dio.get('home', queryParameters: {
        if (keyword != null) 'keyword': keyword,
        if (categoryId != null) 'category_id': categoryId,
      });

      if (response.statusCode == 200) {
        final List data = response.data['data']['data']; // Laravel pagination has data in data
        return data.map((json) => Product.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      print('Error fetching products: $e');
      return [];
    }
  }

  Future<Map<String, dynamic>?> getProductDetail(int id) async {
    try {
      final response = await _apiService.dio.get('home/product/$id');
      if (response.statusCode == 200) {
        final data = response.data['data'];
        return {
          'product': Product.fromJson(data['product']),
          'related_products': (data['related_products'] as List).map((json) => Product.fromJson(json)).toList(),
          'is_favorited': data['is_favorited'] ?? false,
        };
      }
      return null;
    } catch (e) {
      return null;
    }
  }


  Future<Map<String, dynamic>?> getSellerProfile(int sellerId) async {
    try {
      final response = await _apiService.dio.get('seller/$sellerId/profile');
      if (response.statusCode == 200) {
        return response.data;
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<List<Category>> getCategories() async {
    try {
      final response = await _apiService.dio.get('categories');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => Category.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }
}
