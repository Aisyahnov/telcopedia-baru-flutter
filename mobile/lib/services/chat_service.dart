import 'api_service.dart';
import '../models/chat.dart';

class ChatService {
  final ApiService _apiService = ApiService();

  Future<List<Chat>> getChats() async {
    try {
      final response = await _apiService.dio.get('chats');
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => Chat.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<List<ChatMessage>> getMessages(int chatId, {int? afterId}) async {
    try {
      final response = await _apiService.dio.get('chat/$chatId/messages', queryParameters: {
        if (afterId != null) 'after_id': afterId,
      });
      if (response.statusCode == 200) {
        final List data = response.data['data'];
        return data.map((json) => ChatMessage.fromJson(json)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<bool> sendMessage(int chatId, String message) async {
    try {
      final response = await _apiService.dio.post('chat/$chatId/send', data: {
        'message': message,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateMessage(int messageId, String message) async {
    try {
      final response = await _apiService.dio.put('chat/message/$messageId', data: {
        'message': message,
      });
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteMessage(int messageId) async {
    try {
      final response = await _apiService.dio.delete('chat/message/$messageId');
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  Future<Chat?> getOrCreateChat(int sellerId, int? productId) async {
    try {
      final response = await _apiService.dio.post('chat/get-or-create', data: {
        'seller_id': sellerId,
        'product_id': productId,
      });
      if (response.statusCode == 200) {
        return Chat.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}
