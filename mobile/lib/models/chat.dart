import 'user.dart';
import 'product.dart';

class Chat {
  final int id;
  final int user1Id;
  final int user2Id;
  final int? productId;
  final User? user1;
  final User? user2;
  final Product? product;
  final List<ChatMessage> messages;

  Chat({
    required this.id,
    required this.user1Id,
    required this.user2Id,
    this.productId,
    this.user1,
    this.user2,
    this.product,
    required this.messages,
  });

  factory Chat.fromJson(Map<String, dynamic> json) {
    return Chat(
      id: json['id'],
      user1Id: json['user1_id'],
      user2Id: json['user2_id'],
      productId: json['product_id'],
      user1: json['user1'] != null ? User.fromJson(json['user1']) : null,
      user2: json['user2'] != null ? User.fromJson(json['user2']) : null,
      product: json['product'] != null ? Product.fromJson(json['product']) : null,
      messages: json['messages'] != null 
        ? (json['messages'] as List).map((m) => ChatMessage.fromJson(m)).toList()
        : [],
    );
  }
}

class ChatMessage {
  final int id;
  final int chatId;
  final int senderId;
  String message;
  final bool isRead;
  final DateTime createdAt;

  ChatMessage({
    required this.id,
    required this.chatId,
    required this.senderId,
    required this.message,
    required this.isRead,
    required this.createdAt,
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    return ChatMessage(
      id: json['id'],
      chatId: json['chat_id'],
      senderId: json['sender_id'],
      message: json['message'] ?? '',
      isRead: (json['is_read'] == 1 || json['is_read'] == true),
      createdAt: DateTime.parse(json['created_at']),
    );
  }
}
