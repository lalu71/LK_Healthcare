import 'package:flutter/foundation.dart';

import '../models/notification.dart';

class NotificationController extends ChangeNotifier {
  final List<AppNotification> _items = [];
  bool _loading = false;

  bool get loading => _loading;
  List<AppNotification> get items => _items;
  int get unreadCount => _items.where((n) => !n.isRead).length;

  Future<void> fetch() async {
    _loading = true;
    notifyListeners();
    // === Backend integration: GET /api/v1/notifications ===
    await Future.delayed(const Duration(milliseconds: 400));
    if (_items.isEmpty) _items.addAll(_mock());
    _loading = false;
    notifyListeners();
  }

  Future<void> markRead(int id) async {
    final idx = _items.indexWhere((n) => n.id == id);
    if (idx == -1) return;
    final old = _items[idx];
    _items[idx] = AppNotification(
      id: old.id,
      userId: old.userId,
      title: old.title,
      message: old.message,
      type: old.type,
      isRead: true,
      createdAt: old.createdAt,
    );
    notifyListeners();
  }

  Future<void> markAllRead() async {
    for (int i = 0; i < _items.length; i++) {
      final n = _items[i];
      _items[i] = AppNotification(
        id: n.id,
        userId: n.userId,
        title: n.title,
        message: n.message,
        type: n.type,
        isRead: true,
        createdAt: n.createdAt,
      );
    }
    notifyListeners();
  }

  List<AppNotification> _mock() => [
        AppNotification(
          id: 1,
          userId: 1,
          title: 'Appointment Confirmed',
          message: 'Your appointment with Dr. Aarav Mehta on May 13 has been confirmed.',
          type: 'appointment',
          isRead: false,
          createdAt: DateTime.now().subtract(const Duration(minutes: 12)),
        ),
        AppNotification(
          id: 2,
          userId: 1,
          title: 'Prescription Ready',
          message: 'Dr. Aarav Mehta has issued a new prescription for you.',
          type: 'prescription',
          isRead: false,
          createdAt: DateTime.now().subtract(const Duration(hours: 3)),
        ),
        AppNotification(
          id: 3,
          userId: 1,
          title: 'Lab Report Available',
          message: 'Your Lipid Profile report is now available to download.',
          type: 'lab',
          isRead: true,
          createdAt: DateTime.now().subtract(const Duration(days: 1)),
        ),
        AppNotification(
          id: 4,
          userId: 1,
          title: 'Medicine Order Delivered',
          message: 'Order PH1715000001 has been delivered.',
          type: 'pharmacy',
          isRead: true,
          createdAt: DateTime.now().subtract(const Duration(days: 5)),
        ),
      ];
}
