import 'api_service.dart';
import '../models/product.dart';

class FavoriteService {
  final ApiService _apiService = ApiService();

  Future<List<Product>> getFavorites({int page = 1}) async {
    try {
      final response = await _apiService.dio.get('home/favorites?page=$page'); // Assuming this is the route
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) {
          // The API might return Favorite objects that contain products
          if (json['product'] != null) {
            return Product.fromJson(json['product']);
          }
          return Product.fromJson(json);
        }).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<bool> toggleFavorite(int productId) async {
    try {
      final response = await _apiService.dio.post('home/favorite/toggle', data: {
        'product_id': productId,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}
