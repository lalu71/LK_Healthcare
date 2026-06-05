class AppNotification {
  final int id;
  final int userId;
  final String title;
  final String message;
  final String? type;
  final bool isRead;
  final DateTime createdAt;

  AppNotification({
    required this.id,
    required this.userId,
    required this.title,
    required this.message,
    this.type,
    this.isRead = false,
    required this.createdAt,
  });

  factory AppNotification.fromJson(Map<String, dynamic> json) => AppNotification(
        id: json['id'] as int,
        userId: json['user_id'] as int? ?? 0,
        title: json['title'] as String? ?? '',
        message: json['message'] as String? ?? '',
        type: json['type'] as String?,
        isRead: (json['is_read'] as bool?) ?? false,
        createdAt: DateTime.parse(
            json['created_at'] as String? ?? DateTime.now().toIso8601String()),
      );
}
