class SystemNotification {
  final String id;
  final String title;
  final String message;
  final String type;
  final String? actionUrl;
  final DateTime createdAt;
  bool isRead;

  SystemNotification({
    required this.id,
    required this.title,
    required this.message,
    required this.type,
    this.actionUrl,
    required this.createdAt,
    this.isRead = false,
  });

  factory SystemNotification.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>;
    return SystemNotification(
      id: json['id'],
      title: data['title'] ?? 'Notifikasi',
      message: data['message'] ?? '',
      type: data['type'] ?? 'info',
      actionUrl: data['action_url'],
      createdAt: DateTime.parse(json['created_at']),
      isRead: json['read_at'] != null,
    );
  }
}
